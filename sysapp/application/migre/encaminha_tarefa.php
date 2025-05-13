<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');

	// ---> ABRE TRANSACAO COM O BD <--- //
	pg_query($db,"BEGIN TRANSACTION");	
	$sql = utf8_encode("
		UPDATE projetos.tarefas
		SET dt_encaminhamento=CURRENT_TIMESTAMP,
		status_atual = 'AMAN'
		WHERE cd_tarefa    = ".$t."
		AND cd_atividade = ".$a.";

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
		     ".$t.",
		     ".$a.",
		     (SELECT cd_recurso 
			    FROM projetos.tarefas
			   WHERE cd_tarefa    = ".$t."
			     AND cd_atividade = ".$a."),
		     CURRENT_TIMESTAMP,
		     'Encaminhado para execução',
		     'AMAN'
		   )
	");
	
	$ob_resul= @pg_query($db,$sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		// ---> DESFAZ A TRANSACAO COM BD<--- //
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		// ---> COMITA DADOS NO BD <--- //
		pg_query($db,"COMMIT TRANSACTION"); 	   
		$m = fnc_envia_email($a, $t, $db, $tpEmail);
		pg_close($db);
		
		//echo 'frm_tarefa.php?os='.$a.'&c='.$t.'&f='.$_REQUEST['f'];
		//exit;
		header('location: frm_tarefa.php?os='.$a.'&c='.$t.'&f='.$_REQUEST['f']);			
	}
			   
			   
//--------------------------------- Envio de e-mails quando do cadastro de uma nova tarefa:
function fnc_envia_email($cd_atividade, $cd_tarefa, $db, $tp) {
//	echo $num_nao_conf;
		$e = new Email();
		$e->IsHTML();															
		$sql =        " SELECT   t.cd_atividade, u.guerra as executor, t.descricao as descricao, t.programa as programa, u.usuario as usuario, t.prioridade, TO_CHAR(t.dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev, TO_CHAR(t.dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev ";
		$sql = $sql . " FROM 	projetos.tarefas t, projetos.usuarios_controledi u ";
		$sql = $sql . " WHERE 	t.cd_atividade = " . $cd_atividade . " AND t.cd_tarefa = " . $cd_tarefa ;
		$sql = $sql . " AND		t.cd_recurso = u.codigo ";
// -------------------------------------------------------------------------
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		$v_para = $reg['usuario']."@eletroceee.com.br";
		$v_cc = "";
		$v_de = "Controle de Atividades e Tarefas";
// ---------------------------------------------------------- Área da mensagem HTML:
		if ($tp == 'I') {
			$v_assunto = "Nova Tarefa solicitada - nº ". $cd_atividade . "/" . $cd_tarefa;	
			$v_msg = "Prezado(a) ".$reg['executor']  . $vbcrlf;
			$v_msg = $v_msg . "Uma nova Tarefa foi solicitada:"  . $vbcrlf;
		}
		else { 
			$v_assunto = "Encaminhamento da Tarefa - nº ". $cd_atividade . "/" . $cd_tarefa;	
			$v_msg = "Prezado(a) ".$reg['executor']  . $vbcrlf;
			$v_msg = $v_msg . "Atualização da tarefa encaminhada:"  . $vbcrlf;
		}
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Tarefa: " . $cd_tarefa. ", Atividade: ". $reg['cd_atividade']  . $vbcrlf;
		if(trim($reg['prioridade'])=="S")
		{
			$v_msg = $v_msg . "Prioridade: sim" . $vbcrlf; 
		}
		else
		{
			$v_msg = $v_msg . "Prioridade: não" . $vbcrlf;
		}
		
		$v_msg = $v_msg . "Data de início prevista: " . $reg['dt_inicio_prev'] . $vbcrlf;
		$v_msg = $v_msg . "Data de término prevista: " . $reg['dt_fim_prev'] . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
// ----------------------------------------- descrição da tarefa:
		$v_msg = $v_msg . "Descrição: " . $reg['descricao']  . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Mensagem enviada pelo Controle de Atividades"  . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
// =========================================== Novo envio de emails:
		$date = date("d/m/Y");
		$sql =        " insert into projetos.envia_emails ( ";
		$sql = $sql . "		dt_envio, ";
		$sql = $sql . "		de, ";
		$sql = $sql . "		para, ";
		$sql = $sql . "		cc,	";
		$sql = $sql . "		cco, ";
		$sql = $sql . "		assunto, ";
		$sql = $sql . "		texto ";
		$sql = $sql . " ) ";
		$sql = $sql . " VALUES ( ";
		$sql = $sql . "		current_date, ";
		$sql = $sql . "		'$v_de', ";
		$sql = $sql . "		'$v_para', ";
		$sql = $sql . "		'$v_cc', ";
		$sql = $sql . "    	'$v_cco', ";
		$sql = $sql . "    	'" . str_replace("'", "`", $v_assunto) . "', ";
		$sql = $sql . "    	'" . str_replace("'", "`", $v_msg) . "' ";
		$sql = $sql . ")";	 
//	echo $sql;
		pg_exec($db, $sql);
	}

?>