<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_sql = "
				UPDATE projetos.tarefas 
				   SET dt_ok_anal   = NULL,
				       dt_fim_prog  = NULL,
				       status_atual = 'EMAN' 
			     WHERE cd_tarefa    = ".$t." 
			       AND cd_atividade = ".$a.";	
	
				INSERT INTO projetos.tarefa_historico 
							( 
							  cd_tarefa,  	
							  cd_atividade, 	
							  cd_recurso,   	
							  timestamp_alteracao,   	
							  descricao,  				
							  status_atual,
							  ds_obs
							) 
					   VALUES
							( 
							  ".$t.", 
							  ".$a.", 
							  ".$recurso.", 
							  current_timestamp, 
							  'Tarefa Reaberta pelo Analista', 
							  'AMAN',
							  '" . $tthis->db->escape_str($_POST['motivo_tarefa_window']) . "'
							);
	          ";
	
	#### ---> ABRE TRANSACAO COM O BD <--- ####
	pg_query($db,"BEGIN TRANSACTION");
	//echo $tthis->db->escape_str($_POST['motivo_tarefa_window']);exit;	
	$ob_resul = pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 
		fnc_envia_email($a, $t, $tthis->db->escape_str($_POST['motivo_tarefa_window']),$db);		
	}

	header('location: frm_tarefa.php?os='.$a.'&c='.$t.'&f='.$_REQUEST['f']);


	function fnc_envia_email($cd_atividade, $cd_tarefa, $motivo_recusa, $db) 
	{
		$qr_sql = " 
					SELECT t.cd_atividade, 
					       t.cd_tarefa,
						   u.nome AS executor, 
						   t.descricao,
						   u.usuario
					  FROM projetos.tarefas t, 
						   projetos.usuarios_controledi u
					 WHERE t.cd_atividade = ".$cd_atividade." 
					   AND t.cd_tarefa    = ".$cd_tarefa."
					   AND t.cd_recurso   = u.codigo 
			      ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);


		$vbcrlf = chr(10).chr(13);
		
		$v_msg = "Prezado(a) ".$ar_reg['executor'].$vbcrlf.
			     "A seguinte tarefa NÃO FOI ACEITA, sendo rejeitada pelo solicitante:".$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Tarefa: ".$ar_reg['cd_tarefa'].", Atividade: ".$ar_reg['cd_atividade'].$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Descrição:".$vbcrlf.$ar_reg['descricao'].$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.				 
				 "Recusa:".$vbcrlf.$motivo_recusa.$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Mensagem enviada pelo Controle de Atividades".$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf;

		$sql = " 
				INSERT INTO projetos.envia_emails 
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
					   CURRENT_TIMESTAMP,
					   'Controle de Atividades',
					   '".$ar_reg['usuario']."@eletroceee.com.br',
					   '',
					   '',
					   'Recusa da Tarefa - nº ".$ar_reg['cd_atividade']."/".$ar_reg['cd_tarefa']."',
					   '".str_replace("'", "`", $v_msg)."'
					 )
			   ";	 
		@pg_query($db, $sql);
	}
?>