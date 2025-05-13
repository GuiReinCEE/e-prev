<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	#echo '<PRE>'.print_r($_REQUEST,true).'</PRE>'; exit;
	
	#### STATUS DE AGUARDANDO INICIO ####
	$ar_status_inicio["GB"]  = "AISB";
	$ar_status_inicio["GF"]  = "AINF";
	$ar_status_inicio["GRI"] = "AICS";
	$ar_status_inicio["GA"]  = "AIGA";
	$ar_status_inicio["GI"]  = "AINI";
	$ar_status_inicio["GAP"] = "AIST";
	$ar_status_inicio["GC"]  = "GCAI";
	$ar_status_inicio["GJ"]  = "AIGJ";
	$ar_status_inicio["SG"]  = "SGAI";	

	#### STATUS DE CONCLUIDO ####
	$ar_gerencia_concluido["GB"]  = "COSB";
	$ar_gerencia_concluido["GAP"] = "COST";
	$ar_gerencia_concluido["GI"]  = "CONC";
	$ar_gerencia_concluido["GF"]  = "CONF";
	$ar_gerencia_concluido["GA"]  = "COGA";
	$ar_gerencia_concluido["GRI"] = "COCS";
	$ar_gerencia_concluido["GC"]  = "GCCO";
	$ar_gerencia_concluido["GJ"]  = "COGJ";
	$ar_gerencia_concluido["SG"]  = "SGCO";
	
	//-----------------------------------------------------------------------------------------------	
	//  #### $_REQUEST["AT"] ####
	// 	AP = Atendeu Plenamente			Status = "Concluida" e email informando
	//	NA = Não Atendeu				Status = "Em manutenção" e email informando
	//-----------------------------------------------------------------------------------------------	
	
	if($_REQUEST["AT"] == 'AP')
	{
		$qr_sql = "
					UPDATE projetos.atividades SET
						   complemento  = '".(trim($_REQUEST["cp"]) != "" ? "Atividade ATENDEU à necessidade do usuário.".chr(10).chr(10).str_replace("'","´",trim($_REQUEST["cp"])) : "Atividade ATENDEU à necessidade do usuário.")."',
						   status_atual = '".$ar_gerencia_concluido[strtoupper($_REQUEST["aa"])]."',
						   dt_fim_real  = CURRENT_TIMESTAMP
					 WHERE numero = ".intval($_REQUEST["n"]).";
					 
					INSERT INTO projetos.atividade_historico 
						 (
						   cd_atividade, 
						   cd_recurso,	
						   dt_inicio_prev,
						   status_atual, 
						   observacoes 
						 ) 
					VALUES 
						 ( 
						   ".intval($_REQUEST["n"]).", 
						   ".intval($_SESSION['Z']).", 
						   CURRENT_TIMESTAMP, 
						   '".$ar_gerencia_concluido[strtoupper($_REQUEST["aa"])]."',
						   '".(trim($_REQUEST["cp"]) != "" ? "Atividade ATENDEU à necessidade do usuário.".chr(10).chr(10).str_replace("'","´",trim($_REQUEST["cp"])) : "Atividade ATENDEU à necessidade do usuário.")."'
						 );					 
		          ";
	}
	else if($_REQUEST["AT"] == 'NA') 
	{
		$qr_sql = "
					INSERT INTO projetos.atividade_historico 
						 (
						   cd_atividade, 
						   cd_recurso,	
						   dt_inicio_prev,
						   status_atual, 
						   observacoes 
						 ) 
					VALUES 
						 ( 
						   ".intval($_REQUEST["n"]).", 
						   ".intval($_SESSION['Z']).", 
						   CURRENT_TIMESTAMP, 
						   '".$ar_status_inicio[strtoupper($_REQUEST["aa"])]."',
						   'Atividade NÃO ATENDEU à necessidade do usuário: ".chr(10).chr(10)."Complemento:".chr(10).str_replace("'","´",trim($_REQUEST["cp"])).chr(10).chr(10).
						   "Data limite para teste: ' || (SELECT TO_CHAR(dt_limite_testes,'DD/MM/YYYY') FROM projetos.atividades WHERE numero = ".intval($_REQUEST["n"]).")
						 );			   
			
					UPDATE projetos.atividades 
					   SET complemento      = '".str_replace("'","´",trim($_REQUEST["cp"]))."',
						   status_atual     = '".$ar_status_inicio[strtoupper($_REQUEST["aa"])]."',
						   dt_limite_testes = NULL
					 WHERE numero = ".intval($_REQUEST["n"]).";		
		          ";	
	}
	
	#echo '<PRE>'.$qr_sql.'</PRE>'; exit;
	
	if(trim($qr_sql) != "")
	{
		#### ABRE TRANSACAO COM O BD #####
		pg_query($db,"BEGIN TRANSACTION");			
		$ob_resul = @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
			echo '<BR><BR>'; 
			echo '<PRE>'.$qr_sql.'</PRE>'; 
			exit; 
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 
			
			envia_email_conclusao(intval($_REQUEST["n"]));
			
			#### VERICA SE DEVE ABRIR UMA ATIVIDADE #### OS: 33157 - 18/01/2012
			if($_REQUEST["AT"] == 'AP')
			{
				gera_atividade(intval($_REQUEST["n"]));
			}

			@pg_close($db);
			
			header('location: cad_atividade_solic.php?n='.intval($_REQUEST['n']).'&aa='.strtoupper($_REQUEST["aa"]));
			exit;
		}		
	}
	else
	{
		header('location: cad_atividade_solic.php?n='.intval($_REQUEST['n']).'&aa='.strtoupper($_REQUEST["aa"]));
		exit;	
	}

	function gera_atividade($cd_atividade)
	{
		global $db;	
		global $ar_status_inicio;
		
		$qr_sql = "
					SELECT COALESCE(a.fl_abrir_encerrar,'N') AS fl_abrir_encerrar,
					       (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cd_usuario_abrir_ao_encerrar) AS cd_gerencia
					  FROM projetos.atividades a
					 WHERE a.numero = ".intval($cd_atividade)."
				  ";

		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);
		if($ar_reg['fl_abrir_encerrar'] == "S")
		{
			#### GERA NOVA ATIVIADE QUANDO CONCLUI ####
			$qr_sql = "
						INSERT INTO projetos.atividades 
							 ( 
								tipo,                
								dt_cad,                  
								descricao,               
								area,  -- Gerência do atendente (destino)
                                divisao, -- Gerência do solicitante (origem)								
								cod_solicitante,         
								cod_atendente,           
								status_atual,            
								tipo_solicitacao,        
								dt_limite,               
								titulo,                  
								cd_empresa,              
								cd_registro_empregado,   
								cd_sequencia,            
								cd_atendimento,          
								forma,                   
								tp_envio,                
								solicitante,             
								cd_plano,                
								sistema,
								cd_atividade_origem
							 )                        
						SELECT CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
										  WHEN TRIM(indic_02) = 'S' THEN 'S'
										  ELSE NULL
										 END
										FROM projetos.usuarios_controledi 
										WHERE codigo = 203) = 'S' 
									THEN 'S'
								ELSE 'N'
							   END AS tipo,                
							   CURRENT_TIMESTAMP AS dt_cad,                  
							   a.descricao_abrir_ao_encerrar AS descricao,               
							   (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cd_usuario_abrir_ao_encerrar) AS area,                    
							   (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE codigo = a.cod_solicitante) AS area,                    
							   a.cod_solicitante,         
							   a.cd_usuario_abrir_ao_encerrar AS cod_atendente,           
							   '".$ar_status_inicio[$ar_reg['cd_gerencia']]."' AS status_atual,            
							   'GP2V' AS tipo_solicitacao,        
							   (a.dt_limite + ('1 day'::interval))::DATE AS dt_limite,               
							   'Atividade aberta pelo encerramento da Atividade número ' || a.numero::TEXT AS titulo,                  
							   a.cd_empresa,              
							   a.cd_registro_empregado,   
							   a.cd_sequencia,            
							   a.cd_atendimento,          
							   a.forma,                   
							   a.tp_envio,                
							   a.solicitante,             
							   a.cd_plano,                
							   a.sistema,
							   a.numero
						  FROM projetos.atividades a
						 WHERE a.numero = ".intval($cd_atividade)."
						RETURNING numero;						
                      ";

			#echo "<PRE>".$qr_sql."</PRE>"; exit;
					  
			#### ABRE TRANSACAO COM O BD #####
			pg_query($db,"BEGIN TRANSACTION");			
			$ob_resul = @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = str_replace("ERROR:","",pg_last_error($db));
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
				echo '<BR><BR>'; 
				echo '<PRE>'.$qr_sql.'</PRE>'; 
				exit; 
			}
			else
			{
				$ar_reg = pg_fetch_array($ob_resul);
				$cd_atividade_new = intval($ar_reg['numero']);
				#print_r($ar_reg);#exit;
				
				#### COMITA DADOS NO BD ####
				pg_query($db,"COMMIT TRANSACTION"); 
				
				
				envia_email_nova($cd_atividade_new);
			}
		}
	}
	
	function envia_email_conclusao($cd_atividade) 
	{
		global $db;
		global $ar_gerencia_concluido;
		$crlf = chr(10);
	
		$qr_sql = "  
					SELECT a.numero, 
					       a.area,
						   a.divisao, 
						   a.complemento, 	
						   a.titulo,
						   a.descricao, 
						   a.status_atual, 
						   a.problema, 
						   a.solucao,
						   a.cd_atendimento,
						   a.tp_envio, 
						   a.forma, 						   
						   TO_CHAR(a.dt_limite, 'DD/MM/YYYY') AS dt_limite,
						   us.nome AS solicitante_nome,
						   us.usuario || '@eletroceee.com.br' AS solicitante_email,
						   ua.nome AS atendente_nome,
						   ua.usuario || '@eletroceee.com.br' AS atendente_email,						   
					       UPPER(st.descricao) AS ds_status_atual,
						   (CASE WHEN (SELECT COUNT(*) FROM projetos.atividade_anexo at WHERE at.cd_atividade = a.numero) > 0 THEN 'S' ELSE 'N' END) AS fl_anexo,
						   pl.descricao AS plano_nome,
						   p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   p.nome,
						   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf,
						   p.endereco,
						   p.nr_endereco,
						   p.complemento_endereco,
						   p.bairro,
						   p.cidade,
						   p.unidade_federativa AS uf,
						   TO_CHAR(p.cep, 'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
						   p.ddd,
						   p.telefone,
						   p.ddd_celular,
						   p.celular,
						   p.email,
						   p.email_profissional,
						   ts.descricao AS tipo_solicitante,
						   fs.descricao AS forma_solicitacao
					  FROM projetos.atividades a 
					  JOIN projetos.usuarios_controledi us
					    ON us.codigo = a.cod_solicitante
					  JOIN projetos.usuarios_controledi ua
					    ON ua.codigo = a.cod_atendente	
					  JOIN public.listas st
					    ON st.codigo    = a.status_atual 
					   AND st.categoria = 'STAT'
					  LEFT JOIN public.participantes p
					    ON p.cd_empresa            = a.cd_empresa           
					   AND p.cd_registro_empregado = a.cd_registro_empregado
					   AND p.seq_dependencia       = a.cd_sequencia 	
    		          LEFT JOIN public.planos pl
    		            ON pl.cd_plano = a.cd_plano		
                      LEFT JOIN public.listas ts
    		            ON ts.codigo    = a.solicitante
					   AND ts.categoria = 'SDAP'
    		          LEFT JOIN public.listas fs
    		            ON fs.codigo    = a.forma
					   AND fs.categoria = 'FDAP'					   
					 WHERE a.numero = ".intval($cd_atividade)."
			      ";

		$ob_resul = pg_query($db, $qr_sql);
	    $ar_reg = pg_fetch_array($ob_resul);
		
		#echo '<PRE>'.$qr_sql.'</PRE>'; exit;
	
		if(intval($ar_reg["numero"]) > 0)
		{
			#### CABECALHO ####
			$de   = "Controle de Atividades (Solicitado pela ".$ar_reg['divisao'].")";
			$para = $ar_reg['atendente_email'];
			$cc   = $ar_reg['solicitante_email'];
			$cco  = "";
			$assunto = (($ar_reg['status_atual'] == $ar_gerencia_concluido[strtoupper($ar_reg['area'])]) ? "(".$ar_reg['ds_status_atual'].") Atividade nº".$ar_reg["numero"] : "(NÃO ATENDEU) Atividade nº".$ar_reg["numero"]);
		
			#### MENSAGEM ####
			$msg = (($ar_reg['status_atual'] == $ar_gerencia_concluido[strtoupper($ar_reg['area'])]) ? "A atividade abaixo foi ".$ar_reg['ds_status_atual'] : "A atividade abaixo NÃO ATENDEU").$crlf.$crlf;
			
			#### COMPLEMENTO ####
			$msg.= ((trim($ar_reg["complemento"]) != "") ? "Complemento:".$crlf.$ar_reg["complemento"].$crlf.$crlf : "");
			
			#### ATIVIDADE ####
			$msg.= "-------------------------------------------------------------".$crlf;	
			$msg.= "ATIVIDADE".$crlf;    
			$msg.= "-------------------------------------------------------------".$crlf;			
			$msg.= "Número: ".$ar_reg['numero'].$crlf;
			$msg.= "Solicitante: ".$ar_reg['solicitante_nome'].$crlf;
			$msg.= "Atendente: ".$ar_reg['atendente_nome'].$crlf;
			$msg.= "Status: ".$ar_reg['ds_status_atual'].$crlf;
			$msg.= "Data Limite: ".$ar_reg['dt_limite'].$crlf;    			
			$msg.= ($ar_reg['fl_anexo'] == "S" ? "ESTA ATIVIDADE POSSUI ANEXO(S)".$crlf : "");    			
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= "Título: ".$ar_reg['titulo'].$crlf.$crlf; 
			$msg.= "Descrição: ".$crlf.$ar_reg['descricao'].$crlf;    
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= ((($ar_reg['area'] != 'GA') or ($ar_reg['tipo_ativ'] == 'L')) ? "Justificativa:".$crlf.$ar_reg['problema'].$crlf."-------------------------------------------------------------".$crlf : "");
			
			#### MANUTENÇÃO/SOLUÇÃO ####
			$msg.= ((trim($ar_reg['solucao']) == "") ? "Descrição da Manutenção:".$crlf.trim($ar_reg['solucao']).$crlf."-------------------------------------------------------------".$crlf : "");
			
			
			$msg.= "Link: ".base_url()."sysapp/application/migre/cad_atividade_solic.php?n=".$ar_reg['numero'].$crlf; 
			$msg.= "-------------------------------------------------------------".$crlf.$crlf;			
			
			if	(
					($ar_reg["area"] == "GB") or
					($ar_reg["area"] == "GF") or
					($ar_reg["area"] == "GJ") or
					($ar_reg["area"] == "GAP") or 
					($ar_reg["divisao"] == "GAP") or 
					(intval($ar_reg['cd_registro_empregado']) > 0)
				)
			{
				$msg.= "-------------------------------------------------------------".$crlf;
				$msg.= "PARTICIPANTE".$crlf;
				$msg.= "-------------------------------------------------------------".$crlf;
				$msg.= (intval($ar_reg['cd_atendimento']) > 0 ? "Protocolo de atendimento: ".intval($ar_reg['cd_atendimento']).$crlf : ""); 
				$msg.= "Emp/Re/Seq: ".$ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'].$crlf;
				$msg.= (trim($ar_reg['plano_nome']) != "" ? "Plano: ".$ar_reg['plano_nome'].$crlf : "");
				
				if(trim($ar_reg['nome']) != "")
				{
					$msg.=  "Nome: ".$ar_reg['nome'].$crlf.
							"CPF: ".$ar_reg['cpf'].$crlf.
							"Endereço: ".$ar_reg['endereco'].", ".$ar_reg['nr_endereco']."/".$ar_reg['complemento_endereco']." - ".$ar_reg['bairro']." - ".$ar_reg['cep']." - ".$ar_reg['cidade']." - ".$ar_reg['uf'].$crlf.
							"Telefone 1: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
							"Telefone 2: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
							"Email: ".$ar_reg['email']. " / " .$ar_reg['email_profissional'].$crlf;
				}
				$msg.= (trim($ar_reg['tipo_solicitante']) != "" ? "Solicitante: ".$ar_reg['tipo_solicitante'].$crlf : "");
				$msg.= (trim($ar_reg['forma_solicitacao']) != "" ? "Forma de solicitação: ".$ar_reg['forma_solicitacao'].$crlf : "");
				$msg.= (intval($ar_reg['tp_envio']) == 1 ? "Forma de Envio: Correio".$crlf : "");
				$msg.= (intval($ar_reg['tp_envio']) == 2 ? "Forma de Envio: Central de Atendimento".$crlf : "");
				$msg.= (intval($ar_reg['tp_envio']) == 3 ? "Forma de Envio: Email".$crlf : "");
				$msg.= "-------------------------------------------------------------".$crlf.$crlf;
			}		
	
			#### GRAVA EMAIL ####
			$qr_sql = " 
						INSERT INTO projetos.envia_emails 
							 ( 
							   dt_envio, 
							   de, 
							   para, 
							   cc,	
							   cco, 
							   assunto, 
							   texto,
							   cd_evento
							 )
						VALUES 
							 ( 
							   CURRENT_TIMESTAMP,
							   '".$de."', 
							   '".$para."', 
							   '".$cc."',
							   '".$cco."',
							   '".$assunto."',
							   '".str_replace("'","",$msg)."',
							   131
							 )";	
							 
			#### ABRE TRANSACAO COM O BD #####
			pg_query($db,"BEGIN TRANSACTION");			
			$ob_resul = @pg_query($db,$qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = str_replace("ERROR:","",pg_last_error($db));
				#### DESFAZ A TRANSACAO COM BD ####
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
				echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
				echo '<BR><BR>'; 
				echo '<PRE>'.$qr_sql.'</PRE>'; 
				exit; 
			}
			else
			{
				#### COMITA DADOS NO BD ####
				pg_query($db,"COMMIT TRANSACTION"); 
			}	
		}
	}
	
	function envia_email_nova($cd_atividade)
	{
		global $db;
		$crlf = chr(10);
	
		$qr_sql = "  
					SELECT a.numero, 
					       a.area,
						   a.divisao, 
						   a.complemento, 	
						   a.titulo,
						   a.descricao, 
						   a.status_atual, 
						   a.problema, 
						   a.cd_atendimento,
						   a.tp_envio, 
						   a.forma, 						   
						   TO_CHAR(a.dt_limite, 'DD/MM/YYYY') AS dt_limite,
						   us.nome AS solicitante_nome,
						   us.usuario || '@eletroceee.com.br' AS solicitante_email,
						   ua.nome AS atendente_nome,
						   ua.usuario || '@eletroceee.com.br' AS atendente_email,						   
					       UPPER(st.descricao) AS ds_status_atual,
						   (CASE WHEN (SELECT COUNT(*) FROM projetos.atividade_anexo at WHERE at.cd_atividade = a.numero) > 0 THEN 'S' ELSE 'N' END) AS fl_anexo,
						   pl.descricao AS plano_nome,
						   p.cd_empresa,
						   p.cd_registro_empregado,
						   p.seq_dependencia,
						   p.nome,
						   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf,
						   p.endereco,
						   p.nr_endereco,
						   p.complemento_endereco,
						   p.bairro,
						   p.cidade,
						   p.unidade_federativa AS uf,
						   TO_CHAR(p.cep, 'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
						   p.ddd,
						   p.telefone,
						   p.ddd_celular,
						   p.celular,
						   p.email,
						   p.email_profissional,
						   ts.descricao AS tipo_solicitante,
						   fs.descricao AS forma_solicitacao
					  FROM projetos.atividades a 
					  JOIN projetos.usuarios_controledi us
					    ON us.codigo = a.cod_solicitante
					  JOIN projetos.usuarios_controledi ua
					    ON ua.codigo = a.cod_atendente	
					  JOIN public.listas st
					    ON st.codigo    = a.status_atual 
					   AND st.categoria = 'STAT'
					  LEFT JOIN public.participantes p
					    ON p.cd_empresa            = a.cd_empresa           
					   AND p.cd_registro_empregado = a.cd_registro_empregado
					   AND p.seq_dependencia       = a.cd_sequencia 	
    		          LEFT JOIN public.planos pl
    		            ON pl.cd_plano = a.cd_plano		
                      LEFT JOIN public.listas ts
    		            ON ts.codigo    = a.solicitante
					   AND ts.categoria = 'SDAP'
    		          LEFT JOIN public.listas fs
    		            ON fs.codigo    = a.forma
					   AND fs.categoria = 'FDAP'					   
					 WHERE a.numero = ".intval($cd_atividade)."
			      ";

		$ob_resul = pg_query($db, $qr_sql);
	    $ar_reg = pg_fetch_array($ob_resul);


		#### CABECALHO ####
		$de   = "Controle de Atividades (Solicitado pela ".$ar_reg['divisao'].")";
		$para = $ar_reg['atendente_email'];
		$cc   = $ar_reg['solicitante_email'];
		$cco  = "";
		$assunto = "(NOVA) Atividade nº".$ar_reg["numero"];
	
		#### MENSAGEM ####
		$msg.= "-------------------------------------------------------------".$crlf;	
		$msg.= "ATIVIDADE".$crlf;    
		$msg.= "-------------------------------------------------------------".$crlf;			
		$msg.= "Número: ".$ar_reg['numero'].$crlf;
		$msg.= "Solicitante: ".$ar_reg['solicitante_nome'].$crlf;
		$msg.= "Atendente: ".$ar_reg['atendente_nome'].$crlf;
		$msg.= "Status: ".$ar_reg['ds_status_atual'].$crlf;
		$msg.= "Data Limite: ".$ar_reg['dt_limite'].$crlf;    			
		$msg.= ($ar_reg['fl_anexo'] == "S" ? "ESTA ATIVIDADE POSSUI ANEXO(S)".$crlf : "");    			
		$msg.= "-------------------------------------------------------------".$crlf;
		$msg.= "Título: ".$ar_reg['titulo'].$crlf.$crlf; 
		$msg.= "Descrição: ".$crlf.$ar_reg['descricao'].$crlf;    
		$msg.= "-------------------------------------------------------------".$crlf;
		$msg.= ((($ar_reg['area'] != 'GA') or ($ar_reg['tipo_ativ'] == 'L')) ? "Justificativa:".$crlf.$ar_reg['problema'].$crlf."-------------------------------------------------------------".$crlf : "");
		$msg.= "Link: ".base_url()."sysapp/application/migre/cad_atividade_solic.php?n=".$ar_reg['numero'].$crlf; 
		$msg.= "-------------------------------------------------------------".$crlf.$crlf;			
		
		if	(
				($ar_reg["area"] == "GB") or
				($ar_reg["area"] == "GF") or
				($ar_reg["area"] == "GJ") or
				($ar_reg["area"] == "GAP") or 
				($ar_reg["divisao"] == "GAP") or 
				(intval($ar_reg['cd_registro_empregado']) > 0)
			)
		{
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= "PARTICIPANTE".$crlf;
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= (intval($ar_reg['cd_atendimento']) > 0 ? "Protocolo de atendimento: ".intval($ar_reg['cd_atendimento']).$crlf : ""); 
			$msg.= "Emp/Re/Seq: ".$ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'].$crlf;
			$msg.= (trim($ar_reg['plano_nome']) != "" ? "Plano: ".$ar_reg['plano_nome'].$crlf : "");
			
			if(trim($ar_reg['nome']) != "")
			{
				$msg.=  "Nome: ".$ar_reg['nome'].$crlf.
				        "CPF: ".$ar_reg['cpf'].$crlf.
						"Endereço: ".$ar_reg['endereco'].", ".$ar_reg['nr_endereco']."/".$ar_reg['complemento_endereco']." - ".$ar_reg['bairro']." - ".$ar_reg['cep']." - ".$ar_reg['cidade']." - ".$ar_reg['uf'].$crlf.
						"Telefone 1: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
						"Telefone 2: ".$ar_reg['ddd']." - ".$ar_reg['telefone'].$crlf.
						"Email: ".$ar_reg['email']. " / " .$ar_reg['email_profissional'].$crlf;
			}
			$msg.= (trim($ar_reg['tipo_solicitante']) != "" ? "Solicitante: ".$ar_reg['tipo_solicitante'].$crlf : "");
			$msg.= (trim($ar_reg['forma_solicitacao']) != "" ? "Forma de solicitação: ".$ar_reg['forma_solicitacao'].$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 1 ? "Forma de Envio: Correio".$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 2 ? "Forma de Envio: Central de Atendimento".$crlf : "");
			$msg.= (intval($ar_reg['tp_envio']) == 3 ? "Forma de Envio: Email".$crlf : "");
			$msg.= "-------------------------------------------------------------".$crlf.$crlf;
		}

		#### GRAVA EMAIL ####
		$qr_sql = " 
					INSERT INTO projetos.envia_emails 
						 ( 
						   dt_envio, 
						   de, 
						   para, 
						   cc,	
						   cco, 
						   assunto, 
						   texto,
						   cd_evento
						 )
					VALUES 
						 ( 
						   CURRENT_TIMESTAMP,
						   '".$de."', 
						   '".$para."', 
						   '".$cc."',
						   '".$cco."',
						   '".$assunto."',
						   '".str_replace("'","",$msg)."',
						   131
						 )";	
						 
		#### ABRE TRANSACAO COM O BD #####
		pg_query($db,"BEGIN TRANSACTION");			
		$ob_resul = @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = str_replace("ERROR:","",pg_last_error($db));
			#### DESFAZ A TRANSACAO COM BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
			echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
			echo '<BR><BR>'; 
			echo '<PRE>'.$qr_sql.'</PRE>'; 
			exit; 
		}
		else
		{
			#### COMITA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION"); 
		}	
	}	
?>