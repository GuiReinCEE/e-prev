<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');

	pg_query($db,"BEGIN TRANSACTION");
	
	$qr_sql = " 
		         UPDATE projetos.tarefas 
		            SET dt_ok_anal   = current_timestamp, 
					    status_atual = 'CONC', 
						prioridade = 'N' 
				  WHERE cd_tarefa = ".$_REQUEST['t']." 
				    AND cd_atividade = ".$_REQUEST['a']."; 

			INSERT INTO projetos.tarefa_historico 
	                  ( 
					    cd_tarefa, 
					    cd_atividade, 
					    cd_recurso, 
					    timestamp_alteracao, 
					    descricao, 
					    status_atual
					  ) 
			     VALUES
				      ( 
					    ".$_REQUEST['t'].", 
						".$_REQUEST['a'].", 
						".$_REQUEST['recurso'].", 
						current_timestamp, 
						'Conclusão da Tarefa pelo Analista.', 
						'CONC'
					  );";	
				  
	$ob_resul= @pg_query($db,utf8_encode($qr_sql));
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		pg_query($db,"ROLLBACK TRANSACTION");
		echo $ds_erro;
	}
	else
	{
		pg_query($db,"COMMIT TRANSACTION");
		$m = fnc_envia_email($_REQUEST['a'], $_REQUEST['t'], $db, $tpEmail);
		header('location: frm_tarefa.php?os='.$_REQUEST['a'].'&c='.$_REQUEST['t']);
	}				  
	pg_close($db);			  
      
	################################### ENVIA EMAIL #####################################
	function fnc_envia_email($cd_atividade, $cd_tarefa, $db, $tp) 
	{
		$e = new Email();
		$e->IsHTML();															
		$sql = "SELECT t.cd_atividade, 
		               u.guerra AS executor, 
					   t.descricao AS descricao, 
					   t.programa AS programa, 
					   u.usuario AS usuario 
				  FROM projetos.tarefas t, 
				       projetos.usuarios_controledi u 
                 WHERE t.cd_atividade = ".$cd_atividade." 
				   AND t.cd_tarefa    = ".$cd_tarefa."
		  		   AND t.cd_recurso   = u.codigo ";
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		// ---> EMAILS <--- //
		$v_para = $reg['usuario']."@eletroceee.com.br";
		$v_cc   = "";
		$v_cco  = "";
		$v_de   = "Controle de Atividades e Tarefas";
		// ---> ASSUNTO <--- //
		$v_assunto = "Conclusão de Tarefa - nº ".$cd_atividade."/".$cd_tarefa;			
		// ---> CONTEUDO <--- //
		$v_msg = "Prezado(a) ".$reg['executor'].$vbcrlf.
				 "A seguinte tarefa foi considerada como concluída pelo solicitante:".$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Tarefa: " . $num_tarefa. ", Atividade: ". $reg['cd_atividade'].$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Descrição: ".$reg['descricao'].$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf.
				 "Mensagem enviada pelo Controle de Atividades".$vbcrlf.
				 "-------------------------------------------------------------".$vbcrlf;
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