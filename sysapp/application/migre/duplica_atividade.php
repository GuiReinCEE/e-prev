<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');
	include_once('inc/class.Email.inc.php');
	
	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	
	#### PEGA NEXTVAL DA SEQUENCE DO CAMPO ####
	$cd_nova_atividade = getNextval("projetos", "atividades", "numero", $db); 
	$sql = " 
	         -- DUPLICA OS
	         INSERT INTO projetos.atividades 
   	              (
				    numero, tipo, dt_cad, descricao, area, dt_inicio_prev, sistema, problema, solucao, dt_inicio_real, status_atual,
	                complexidade, prioridade, negocio_fim, prejuizo, legislacao, situacao, dependencia, dias_realizados, cliente_externo, concorrencia, tarefa,
	                tipo_solicitacao, numero_dias, dt_fim_prev, periodicidade, dt_deacordo, observacoes, divisao, origem, recurso, cod_atendente,
	                cod_solicitante, dt_limite, dt_limite_testes, ok, complemento, num_dias_adicionados, titulo, cd_empresa, cd_registro_empregado, cd_sequencia,
	                dt_retorno, pertinencia, cd_cenario, opt_grafica, opt_eletronica, opt_evento, opt_anuncio, opt_folder, opt_mala, opt_cartaz, opt_cartilha, 
	                opt_site, opt_outro, cores, formato, gramatura, quantia, custo, cc, pacs, patracs, nacs, cacs, lacs, dacs, forma, solicitante, cd_plano, numero_at_origem
				  ) 
		          (
				    SELECT ".$cd_nova_atividade.", tipo, current_timestamp, descricao, area, dt_inicio_prev, sistema, problema, solucao, dt_inicio_real, 'AINI', 
	                       complexidade, prioridade, negocio_fim, prejuizo, legislacao, situacao, dependencia, dias_realizados, cliente_externo, concorrencia, tarefa,
	                       tipo_solicitacao, numero_dias, dt_fim_prev, periodicidade, dt_deacordo, observacoes, divisao, origem, recurso, cod_atendente, 
	                       cod_solicitante, dt_limite, dt_limite_testes, ok, complemento, num_dias_adicionados, titulo, cd_empresa, cd_registro_empregado, cd_sequencia, 
	                       dt_retorno, pertinencia, cd_cenario, opt_grafica, opt_eletronica, opt_evento, opt_anuncio, opt_folder, opt_mala, opt_cartaz, opt_cartilha, 
	                       opt_site, opt_outro, cores, formato, gramatura, quantia, custo, cc, pacs, patracs, nacs, cacs, lacs, dacs, forma, solicitante, cd_plano, numero 
					  FROM projetos.atividades 
					 WHERE numero = ".$c."
				  );

            -- INSERE NO HISTORICO DA NOVA ATIVIDADE
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
				   ".$cd_nova_atividade.", 
				   ".$_SESSION['Z'].",
				   CURRENT_TIMESTAMP,
				   'AINI',
				   'Atividade duplicada, atividade anterior número ".$c."' 
				 );
				  
            -- ATUALIZA O STATUS DA ATIVIDADE DUPLICADA			
		    /*UPDATE projetos.atividades
			   SET status_atual = 'CANC'
			 WHERE numero = ".$c.";*/
			
            -- INSERE NO HISTORICO DA ATIVIDADE DUPLICADA
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
				   ".$c.", 
				   ".$_SESSION['Z'].",
				   CURRENT_TIMESTAMP,
				   'CANC',
				   'Atividade duplicada, nova atividade número ".$cd_nova_atividade."'
				 );
		   ";
	//echo "<PRE><br>".$sql;
	//exit;
	

	$ob_resul = @pg_query($db,$sql);
	if(!$ob_resul)
	{
		#### PEGA ERRO OCORRIDO NO BD ####
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($ob_resul));
		echo "<PRE><br>".$ds_erro;
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		exit;
	}
	else
	{
		#### GRAVA OS DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
		
		enviaEmailNova($cd_nova_atividade, $db);
		enviaEmailDuplicada($c, $cd_nova_atividade, $db);
		
		header('location: lst_atividades.php');		
		pg_close($db);	
	}
	
	
	#### ENVIA EMAIL ####
	function enviaEmailNova($num_atividade, $db) 
	{
		$sql = " 
                SELECT a.numero, 
				       ltp.descricao AS tipo, 
					   a.descricao AS descati, 
					   u1.usuario AS solicitante, 
					   u1.nome AS nomesolic, 
					   a.cod_testador, 
					   a.tipo as tipo_ativ, 
					   a.problema, 
					   a.solucao, 
					   a.observacoes, 
					   a.area,
					   TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_testes, 
					   u2.usuario as atendente, 
					   u2.nome as nomeatend, 
					   a.status_atual, 
					   u1.formato_mensagem as fmens_solic, 
					   u1.e_mail_alternativo as emailalt_solic,
					   u2.formato_mensagem as fmens_atend, 
					   u2.e_mail_alternativo as emailalt_atend,
					   lsa.descricao as situacao
				  FROM projetos.atividades a,
				       projetos.usuarios_controledi u1,
					   projetos.usuarios_controledi u2,
					   public.listas ltp,
					   public.listas lsa
				 WHERE u1.codigo     = a.cod_solicitante
				   AND u2.codigo     = a.cod_atendente
				   AND ltp.codigo    = a.tipo 
				   AND ltp.categoria = 'TPAT'
				   AND lsa.codigo    = a.status_atual 
				   AND lsa.categoria = 'STAT' 
				   AND a.numero      = ".$num_atividade;
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		
		if ($reg['nomeatend'] == $reg['nomesolic']) 
		{		
			$v_msg = "Prezada(o) ".$reg['nomeatend'].$vbcrlf;
		}													
		else 
		{												
			$v_msg = "Prezadas(os) ".$reg['nomeatend']." e ".$reg['nomesolic'].$vbcrlf;
		}													
		
		$v_assunto = "Nova atividade solicitada - nº".$num_atividade;
	
		$v_solicitante = str_replace("Todos","", $reg['solicitante']);
		$v_atendente   = str_replace("Todos","", $reg['atendente']);		 
		
		$v_para = $v_atendente."@eletroceee.com.br";
		if ($reg['emailalt_atend'] != '') 
		{
			$v_para = $v_para.'; '.$reg['emailalt_atend'];	
		}
		
		if($v_atendente != $v_solicitante)
		{
			$v_cc   = $v_solicitante."@eletroceee.com.br";
			if ($reg['emailalt_solic'] != '')
			{					
				$v_cc = $v_cc.'; '.$reg['emailalt_solic'];
			}			
		}
		

		$v_msg.="Foi enviada uma solicitação de ".$reg['tpsolic'].$vbcrlf;

		// ---> Área da mensagem texto: <--- //
		$v_msg = $v_msg . "Solicitante: " . $reg['nomesolic'] .$vbcrlf;
		$v_msg = $v_msg . "Atendente: " . $reg['nomeatend'] .$vbcrlf;
		$v_msg = $v_msg . "Atividade:" . $reg['numero'] .$vbcrlf;
		$v_msg = $v_msg . "Situação:" . $reg['situacao'] . $email_alt .$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição: ".$vbcrlf . $reg['descati'].$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Link para Atividade: ".$vbcrlf;
		$v_msg = $v_msg . "http://www.e-prev.com.br/controle_projetos/cad_atividade_solic.php?n=" . $num_atividade . "&aa=".$reg['area']."&TA=A" . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Justificativa da Manutenção: " . $vbcrlf . $reg['problema'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição da Manutenção: ".$vbcrlf . $reg['solucao'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Observações: ". $vbcrlf . $reg['observacoes'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Data limite para testes: ". $reg['dt_limite_testes'] . $vbcrlf;
		$v_msg = $v_msg . "Testador: ". $v_nome_testador . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Esta mensagem foi enviada pelo Controle de Atividades.";
		if ($reg['tipo_ativ'] == 'L') {											// 23/03/2007
			$v_cco = "amedeiros@eletroceee.com.br";
		} else {
			$v_cco = "";
		}
	// -------------------------------------------------------------	  
		$v_de = "Controle de Atividades";
		// ---> GRAVA EMAIL <--- //	
		$sql = " insert into projetos.envia_emails 
						   ( 
							 dt_envio, 
							 de,
							 para, 
							 cc,	
							 cco, 
							 assunto,
							 texto 
						   ) 
					  VALUES
						   ( 
							 current_date, 
							 '".$v_de."',
							 '".$v_para."', 
							 '".$v_cc."', 
							 '".$v_cco."',
							 '".str_replace("'", "`", $v_assunto)."', 
							 '".str_replace("'", "`", $v_msg)."'
						   )";	
		@pg_query($db, $sql);
	}	
	
	function enviaEmailDuplicada($num_atividade, $num_atividade_nova, $db) 
	{
		$sql = " 
                SELECT a.numero, 
				       ltp.descricao AS tipo, 
					   a.descricao AS descati, 
					   u1.usuario AS solicitante, 
					   u1.nome AS nomesolic, 
					   a.cod_testador, 
					   a.tipo as tipo_ativ, 
					   a.problema, 
					   a.solucao, 
					   a.observacoes, 
					   a.area,
					   TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_testes, 
					   u2.usuario as atendente, 
					   u2.nome as nomeatend, 
					   a.status_atual, 
					   u1.formato_mensagem as fmens_solic, 
					   u1.e_mail_alternativo as emailalt_solic,
					   u2.formato_mensagem as fmens_atend, 
					   u2.e_mail_alternativo as emailalt_atend,
					   lsa.descricao as situacao
				  FROM projetos.atividades a,
				       projetos.usuarios_controledi u1,
					   projetos.usuarios_controledi u2,
					   public.listas ltp,
					   public.listas lsa
				 WHERE u1.codigo     = a.cod_solicitante
				   AND u2.codigo     = a.cod_atendente
				   AND ltp.codigo    = a.tipo 
				   AND ltp.categoria = 'TPAT'
				   AND lsa.codigo    = a.status_atual 
				   AND lsa.categoria = 'STAT' 
				   AND a.numero      = ".$num_atividade;
		$rs  = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		
		if ($reg['nomeatend'] == $reg['nomesolic']) 
		{		
			$v_msg = "Prezada(o) ".$reg['nomeatend'].$vbcrlf;
		}													
		else 
		{												
			$v_msg = "Prezadas(os) ".$reg['nomeatend']." e ".$reg['nomesolic'].$vbcrlf;
		}													
		
		$v_assunto = "Alteração de Situação da Atividade nº ".$num_atividade." (DUPLICADA)";
	
		$v_solicitante = str_replace("Todos","", $reg['solicitante']);
		$v_atendente   = str_replace("Todos","", $reg['atendente']);		 
		
		$v_para = $v_atendente."@eletroceee.com.br";
		if ($reg['emailalt_atend'] != '') 
		{
			$v_para = $v_para.'; '.$reg['emailalt_atend'];	
		}
		
		if($v_atendente != $v_solicitante)
		{
			$v_cc   = $v_solicitante."@eletroceee.com.br";
			if ($reg['emailalt_solic'] != '')
			{					
				$v_cc = $v_cc.'; '.$reg['emailalt_solic'];
			}			
		}
		

		$v_msg.= "Atividade foi duplicada.".$vbcrlf;
		$v_msg.= "Foi gerada uma nova atividade (".$num_atividade_nova.") com a seguinte definição.".$vbcrlf;
		// ---> Área da mensagem texto: <--- //
		$v_msg = $v_msg . "Solicitante: " . $reg['nomesolic'] .$vbcrlf;
		$v_msg = $v_msg . "Atendente: " . $reg['nomeatend'] .$vbcrlf;
		$v_msg = $v_msg . "Atividade:" . $reg['numero'] .$vbcrlf;
		$v_msg = $v_msg . "Situação:" . $reg['situacao'] . $email_alt .$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição: ".$vbcrlf . $reg['descati'].$vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Link para Atividade: ".$vbcrlf;
		$v_msg = $v_msg . "http://www.e-prev.com.br/controle_projetos/cad_atividade_solic.php?n=" . $num_atividade . "&aa=".$reg['area']."&TA=A" . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Justificativa da Manutenção: " . $vbcrlf . $reg['problema'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Descrição da Manutenção: ".$vbcrlf . $reg['solucao'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Observações: ". $vbcrlf . $reg['observacoes'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Data limite para testes: ". $reg['dt_limite_testes'] . $vbcrlf;
		$v_msg = $v_msg . "Testador: ". $v_nome_testador . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Esta mensagem foi enviada pelo Controle de Atividades.";
		if ($reg['tipo_ativ'] == 'L') {											// 23/03/2007
			$v_cco = "amedeiros@eletroceee.com.br";
		} else {
			$v_cco = "";
		}
	// -------------------------------------------------------------	  
		$v_de = "Controle de Atividades";
		// ---> GRAVA EMAIL <--- //	
		$sql = " insert into projetos.envia_emails 
						   ( 
							 dt_envio, 
							 de,
							 para, 
							 cc,	
							 cco, 
							 assunto,
							 texto 
						   ) 
					  VALUES
						   ( 
							 current_date, 
							 '".$v_de."',
							 '".$v_para."', 
							 '".$v_cc."', 
							 '".$v_cco."',
							 '".str_replace("'", "`", $v_assunto)."', 
							 '".str_replace("'", "`", $v_msg)."'
						   )";	
		@pg_query($db, $sql);
	}



?>