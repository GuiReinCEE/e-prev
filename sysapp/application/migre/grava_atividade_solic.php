<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');

	#### STATUS DE AGUARDANDO INICIO ####
	#$ar_status_inicio = Array('AINI','AIST', 'AICS', 'AINF', 'GCAI', 'AIGA','AISB','AIGJ','SGAI');	
	$ar_status_inclusao["GB"]  = "AISB";
	$ar_status_inclusao["GF"]  = "AINF";
	$ar_status_inclusao["GRI"] = "AICS";
	$ar_status_inclusao["GA"]  = "AIGA";
	$ar_status_inclusao["GI"]  = "AINI";
	$ar_status_inclusao["GAP"] = "AIST";
	$ar_status_inclusao["GC"]  = "GCAI";
	$ar_status_inclusao["GJ"]  = "AIGJ";
	$ar_status_inclusao["SG"]  = "SGAI";	
	
	#### STATUS DE CONCLUIDO ####
	$ar_status_concluido = Array('COSB','COST','CONC','CONF','COGA','COCS','GCCO','COGJ','SGCO');

	#echo "<PRE>"; print_r($_POST); 

	$cbo_sistema          = ($cbo_sistema           == '' ? 'NULL' : "'".str_replace("'","´", $cbo_sistema)."'" );
	$txt_dt_inicio_prev   = ($txt_dt_inicio_prev    == '' ? 'NULL' : "TO_TIMESTAMP('".trim($txt_dt_inicio_prev)."','DD/MM/YYYY HH24:MI:SS')" );
	$txt_dt_inicio_real   = ($txt_dt_inicio_real    == '' ? 'NULL' : "TO_TIMESTAMP('".trim($txt_dt_inicio_real)."','DD/MM/YYYY HH24:MI:SS')" );
	$txt_dt_fim_prev      = ($txt_dt_fim_prev       == '' ? 'NULL' : "TO_TIMESTAMP('".trim($txt_dt_fim_prev)."','DD/MM/YYYY HH24:MI:SS')" );
	$txt_dt_fim_real      = ($txt_dt_fim_real       == '' ? 'NULL' : "TO_TIMESTAMP('".trim($txt_dt_fim_real)."','DD/MM/YYYY HH24:MI:SS')" );
	$txt_dt_env_teste     = ($txt_dt_env_teste      == '' ? 'NULL' : "TO_TIMESTAMP('".trim($txt_dt_env_teste)."','DD/MM/YYYY HH24:MI:SS')");
	$txt_dt_retorno       = ($txt_dt_retorno        == '' ? 'NULL' : "TO_DATE('".trim($dt_retorno)."','DD/MM/YYYY HH24:MI:SS')");
	$txt_descricao        = ($txt_descricao         == '' ? 'NULL' : "'".str_replace("'","´", $txt_descricao)."'" );
	$txt_problema         = ($txt_problema          == '' ? 'NULL' : "'".str_replace("'","´", $txt_problema)."'" );
	$txt_solucao          = ($txt_solucao           == '' ? 'NULL' : "'".str_replace("'","´", $txt_solucao)."'" );
	$txt_titulo           = ($titulo                == '' ? 'NULL' : "'".str_replace("'","´", $titulo)."'" ); 
	$txt_empresa          = ($cbo_patrocinadora     == '' ? 'NULL' : "'".str_replace("'","´", $cbo_patrocinadora)."'" ); 
	$txt_re               = ($cd_registro_empregado == '' ? 'NULL' : "'".str_replace("'","´", $cd_registro_empregado)."'" ); 
	$txt_seq              = ($sequencia             == '' ? 'NULL' : "'".str_replace("'","´", $sequencia)."'" ); 
	$txt_cd_atendimento   = ($cd_atendimento 		== '' ? 'NULL' : "'".str_replace("'","´", $cd_atendimento)."'" );
	$_REQUEST['cbo_area'] = ($_REQUEST['cbo_area']  == '' ? 'NULL' : "'".$_REQUEST['cbo_area']."'" );
	$txt_quantia          = ($quantia == '' ? '0' : $quantia);
	$txt_custo            = ($custo == '' ? '0' : $custo);
	$txt_cc               = ($cc == '' ? '0' : $cc);
	$txt_cbo_patroc       = ($cbo_patroc == '' ? '0' : $cbo_patroc);
	$cbo_nacs             = ($cbo_nacs == '' ? '0' : $cbo_nacs);
	$cbo_lacs             = ($cbo_lacs == '' ? '0' : $cbo_lacs);
	$cbo_plan             = ($cbo_plan == '' ? '0' : $cbo_plan);
	$txt_periodicidade    = ($cbo_periodicidade == '' ? 'I' : $cbo_periodicidade);
	$cbo_tipo_manutencao  = ($cbo_tipo_manutencao == '' ? 'MANU' : $cbo_tipo_manutencao);
	
   	/**
     * $cbo_fedap
     * novo campo criado para solicitações realizadas:
     * 
     * - pela GAP
     * - para GAP
     * - para GB
     */
	$cbo_fedap = (($_POST['cbo_fedap'] == '') ? '0' : trim($_POST['cbo_fedap']));
	
    // $aa = divisão atendente

	if(intval($_POST['n']) == 0) 
	{
		$sql = " 
				INSERT INTO projetos.atividades 
					 ( 
						tipo,                
						dt_cad,                  
						descricao,               
						cd_recorrente,           
						area,                    
						dt_inicio_prev,          
						problema,                
						solucao,                 
						dt_inicio_real,          
						cod_solicitante,         
						cod_atendente,           
						status_atual,            
						complexidade,            
						tipo_solicitacao,        
						dt_fim_prev,             
						divisao,                 
						dt_fim_real,             
						dt_env_teste,            
						dt_limite,               
						titulo,                  
						cd_empresa,              
						cd_registro_empregado,   
						cd_sequencia,            
						cd_atendimento,          
						dt_retorno,              
						forma,                   
						tp_envio,                
						solicitante,             
						cd_plano,                
						sistema,
						cd_atividade_origem,
						fl_abrir_encerrar,
						cd_usuario_abrir_ao_encerrar,
						descricao_abrir_ao_encerrar
					 )                        
				VALUES 
					 (                 
						CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
											   WHEN TRIM(indic_02) = 'S' THEN 'S'
											   ELSE NULL
										  END
									 FROM projetos.usuarios_controledi 
									WHERE codigo = ".$cbo_analista.") = 'S' 
							  THEN 'S'
							  ELSE '".$txt_periodicidade."'
						END,
						CURRENT_TIMESTAMP,         
						".$txt_descricao.",            
						'".$cbo_recorrente."',         
						'".strtoupper($aa)."',
						".$txt_dt_inicio_prev.",       
						".$txt_problema.",             
						".$txt_solucao.",              
						".$txt_dt_inicio_real.",       
						".$cbo_solicitante.",          
						".$cbo_analista.",             
						'".(trim($ar_status_inclusao[strtoupper($aa)]) != "" ? trim($ar_status_inclusao[strtoupper($aa)]) : 'AINI')."',       
						'".$cbo_complexidade."',       
						'".$cbo_tipo_manutencao."',    
						".$txt_dt_fim_prev.",          
						(SELECT divisao FROM projetos.usuarios_controledi WHERE codigo = ".$cbo_solicitante."), 
						".$txt_dt_fim_real.",         
						".$txt_dt_env_teste.",        
						".(trim($_POST['dt_limite']) != '' ? "TO_DATE('".trim($_POST['dt_limite'])."','DD/MM/YYYY')" : "DEFAULT").",           
						".$txt_titulo.",              
						".$txt_empresa.",             
						".$txt_re.",                  
						".$txt_seq.",                 
						".$txt_cd_atendimento.",      
						".$txt_dt_retorno.",          
						'".$cbo_fdap."',              
						".$cbo_fedap.",               
						'".$cbo_sdap."',              
						".$cbo_plan.",                
						".$cbo_sistema.",
						".(intval($_REQUEST['cd_atividade_origem']) == 0 ? "DEFAULT" : intval($_REQUEST['cd_atividade_origem'])).",
						".(trim($_REQUEST['fl_abrir_encerrar']) == "" ? "DEFAULT" : "'".trim($_REQUEST['fl_abrir_encerrar'])."'").",
						".(intval($_REQUEST['cd_usuario_abrir_ao_encerrar']) == 0 ? "DEFAULT" : intval($_REQUEST['cd_usuario_abrir_ao_encerrar'])).",
						".(trim($_REQUEST['descricao_abrir_ao_encerrar']) == "" ? "DEFAULT" : "'".trim($_REQUEST['descricao_abrir_ao_encerrar'])."'")."
					 )
				RETURNING numero;
				";
    }
    else 
    {
        $sql = "
				UPDATE projetos.atividades  
                   SET cd_recorrente  = '".$cbo_recorrente."',
                       cd_atendimento = ".$txt_cd_atendimento.",
                       tp_envio       = ".pg_escape_string($cbo_fedap).",					   
			   "; 
		$sql.= (((trim($txt_descricao) != "") and (trim($txt_descricao) != "NULL")) ? " descricao = ".$txt_descricao.", " : "");
		$sql.= (((trim($_REQUEST['cbo_area']) != "") and (trim($_REQUEST['cbo_area']) != "NULL")) ? " area = ".$_REQUEST['cbo_area'].", " : "");
		$sql.= (((trim($txt_problema) != "") and (trim($txt_problema) != "NULL")) ? " problema = ".$txt_problema.", " : "");    
		$sql.= (((trim($txt_solucao) != "") and (trim($txt_solucao) != "NULL")) ? " solucao = ".$txt_solucao.", " : "");      
		$sql.= (((trim($cbo_solicitante) != "") and (trim($cbo_solicitante) != "NULL")) ? " cod_solicitante = ".$cbo_solicitante.", " : "");       
		$sql.= (((trim($cbo_analista) != "") and (trim($cbo_analista) != "NULL")) ?  " cod_atendente = ".$cbo_analista.", " : "");
		$sql.= (((trim($cbo_tipo_manutencao) != "") and (trim($cbo_tipo_manutencao) != "NULL")) ? " tipo_solicitacao = '".$cbo_tipo_manutencao."', " : "");
		$sql.= (trim($_POST['dt_limite']) != '' ? " dt_limite = TO_DATE('".trim($_POST['dt_limite'])."','DD/MM/YYYY')," : " dt_limite = DEFAULT,");
		$sql.= (((trim($txt_titulo) != "") and (trim($txt_titulo) != "NULL")) ? " titulo = ".$txt_titulo.", " : "");
		$sql.= (((trim($txt_empresa) != "") and (trim($txt_empresa) != "NULL")) ? " cd_empresa = ".$txt_empresa.", " : "");
		$sql.= (((trim($txt_re) != "") and (trim($txt_re) != "NULL")) ? " cd_registro_empregado = ".$txt_re.", " : "");
		$sql.= (((trim($txt_seq) != "") and (trim($txt_seq) != "NULL")) ? " cd_sequencia = ".$txt_seq.", " : "");
        $sql.= (((trim($txt_dt_retorno) != "") and (trim($txt_dt_retorno) != "NULL")) ? " dt_retorno = ".$txt_dt_retorno.", " : "");
        $sql.= (((trim($cbo_fdap) != "") and (trim($cbo_fdap) != "NULL")) ? " forma = '".$cbo_fdap."', " : "");
        $sql.= (((trim($cbo_sdap) != "") and (trim($cbo_sdap) != "NULL")) ? " solicitante = '".$cbo_sdap."', " : "");
        $sql.= (((trim($cbo_plan) != "") and (trim($cbo_plan) != "NULL")) ? " cd_plano = ".$cbo_plan."," : "");
        $sql.= (((trim($cbo_sistema) != "") and (trim($cbo_sistema) != "NULL")) ? " sistema = ".$cbo_sistema."," : "");            

		#### SOMENTE PARA GA, COMPLEMENTAÇÃO DE INFORMAÇÕES ####
		$sql.= (($_POST['fl_ga_informacoes_complementares'] == "S") ? " status_atual = 'AIGA', " : "");
  
        if((trim($cbo_periodicidade) != "")   and (trim($cbo_periodicidade) != "NULL") and trim($cbo_analista != ""))   
        { 
            $sql.= " tipo = CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
                                                   WHEN TRIM(indic_02) = 'S' THEN 'S'
                                                   ELSE NULL
                                               END
                                          FROM projetos.usuarios_controledi 
                                         WHERE codigo = ".$cbo_analista.") = 'S' 
                                 THEN 'S'
                                 ELSE '".$txt_periodicidade."'
                             END, "; 
        }

        $sql .= "    
		               numero = numero 
                 WHERE numero = ".intval($_POST['n'])."
				";
    }

	#echo "<PRE>".$sql."</PRE>"; exit;
	
	
	#### ABRE TRANSACAO COM O BD #####
	pg_query($db,"BEGIN TRANSACTION");			
	$ob_resul = @pg_query($db,$sql);
	if(!$ob_resul)
	{
		$ds_erro = str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 16pt;">ERRO</h1>'; 
		echo '<h1 style="color: red; font-weight:bold; font-family: calibri, arial; font-size: 12pt;">'.$ds_erro.'</h1>'; 
		echo '<BR><BR>'; 
		echo '<PRE>'.$sql.'</PRE>'; 
		exit; 
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 

		$fl_acao = 'A'; // Alteração de Status
		
		#### SE FOR INCLUSAO ####
		if(intval($_POST['n']) < 1)
		{
			$ar_reg = pg_fetch_array($ob_resul);
			$_POST['n'] = intval($ar_reg['numero']);
			$fl_acao = 'I'; // Inclusão de novo item
		}

		envia_email(intval($_POST['n']), $fl_acao);
		
		@pg_close($db);
		
		header('location: cad_atividade_solic.php?n='.intval($_POST['n']).'&aa='.$aa.'&TA=A');
		
		exit;
	}	

   
    function envia_email($cd_atividade, $fl_acao)
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
		$assunto = ($fl_acao == "I" ? "(NOVA) Atividade nº".$ar_reg["numero"] : "(ALTERADA - ".$ar_reg['ds_status_atual'].") Atividade nº".$ar_reg["numero"]);
		
		if(($fl_acao != "I") and (trim($_POST['dt_limite']) <> trim($_POST['dt_limite_old'])))
		{
			$assunto = "DT LIMITE ALTERADA - ".$assunto; 
		}
	
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
				($ar_reg["area"] == "GP") or 
				($ar_reg["divisao"] == "GP") or 				
				(intval($ar_reg['cd_registro_empregado']) > 0)
			)
		{
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= "PARTICIPANTE".$crlf;
			$msg.= "-------------------------------------------------------------".$crlf;
			$msg.= (intval($ar_reg['cd_atendimento']) > 0 ? "Protocolo de atendimento: ".intval($ar_reg['cd_atendimento']).$crlf : ""); 
			$msg.= "Emp/Re/Seq: ".$ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'].$crlf;
			//$msg.= (trim($ar_reg['plano_nome']) != "" ? "Plano: ".$ar_reg['plano_nome'].$crlf : "");
			
			$qr_part = "
						SELECT pa.sigla AS empresa,
							   p.nome,
							   funcoes.format_cpf(TO_CHAR(p.cpf_mf,'FM00000000000')) AS cpf,
							   p.email,
							   p.email_profissional,
							   TO_CHAR(COALESCE(p.ddd,0),'FM(00) ') || TO_CHAR(COALESCE(p.telefone,0),'FM900000000') AS telefone,
							   TO_CHAR(COALESCE(p.ddd_celular,0),'FM(00) ') || TO_CHAR(COALESCE(p.celular,0),'FM900000000') AS celular,
							   p.endereco,
							   p.nr_endereco,
							   p.complemento_endereco,
							   p.bairro,
							   p.cidade,
							   p.unidade_federativa AS uf,
							   TO_CHAR(p.cep,'FM99999') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep
						  FROM public.participantes p 
						  JOIN public.patrocinadoras pa
							ON pa.cd_empresa = p.cd_empresa 	
						 WHERE p.cd_empresa            = ".intval($ar_reg['cd_empresa'])."
						   AND p.cd_registro_empregado = ".intval($ar_reg['cd_registro_empregado'])."
						   AND p.seq_dependencia       = ".intval($ar_reg['seq_dependencia'])."
			           ";
			$ob_part = pg_query($db, $qr_part);
			$ar_part = pg_fetch_array($ob_part);
			
			$msg.= (trim($ar_part['empresa'])            != "" ? "Patrocinadora/Instituidor: ".$ar_part['empresa'].$crlf : "");
			$msg.= $crlf;
			
			$msg.= (trim($ar_part['nome'])               != "" ? "Nome: ".$ar_part['nome'].$crlf : "");
			$msg.= (trim($ar_part['cpf'])                != "" ? "CPF: ".$ar_part['cpf'].$crlf : "");
			$msg.= (trim($ar_part['email'])              != "" ? "Email: ".$ar_part['email'].$crlf : "");
			$msg.= (trim($ar_part['email_profissional']) != "" ? "Email profissional: ".$ar_part['email_profissional'].$crlf : "");
			$msg.= (trim($ar_part['telefone'])           != "" ? "Telefone: ".$ar_part['telefone'].$crlf : "");
			$msg.= (trim($ar_part['celular'])            != "" ? "Telefone: ".$ar_part['celular'].$crlf : "");
			$msg.= (trim($ar_part['endereco'])           != "" ? "Endereço: ".$ar_part['endereco'].", ".$ar_part['nr_endereco'].", ".$ar_part['complemento_endereco']." ".$ar_part['bairro'].$crlf : "");
			$msg.= (trim($ar_part['cidade'])             != "" ? "Cidade - UF: ".$ar_part['cidade']." - ".$ar_part['uf'].$crlf : "");
			$msg.= (trim($ar_part['cep'])                != "" ? "CEP: ".$ar_part['cep'].$crlf : "");
			
			$msg.= $crlf;
			
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
						   '".pg_escape_string($assunto)."',
						   '".pg_escape_string($msg)."',
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