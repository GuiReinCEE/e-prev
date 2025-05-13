<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include('oo/start.php');
    include('inc/ePrev.Enums.php');
    using(array('projetos.tarefas', 'projetos.tarefa_checklist'));

    $tarefas = tarefas::select_1( $_REQUEST['a'], $_REQUEST['t'] );

    if($tarefas[0]['fl_checklist']=="S")
    {
    	$os = $_REQUEST['a'];
	    $c = $_REQUEST['t'];
		$tarefas = tarefas::select_1($os, $c);
		
		$f = strtolower($tarefas[0]['fl_tarefa_tipo']);
	    if( $tarefas[0]['fl_tarefa_tipo']=="R"||$tarefas[0]['fl_tarefa_tipo']=="F"||$tarefas[0]['fl_tarefa_tipo']=="A" )
	    {
		    $tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_ORACLE;
	    }
	    else
	    {
	    	$tipo = enum_projetos_tarefa_checklist_tipo::CHECKLIST_WEB;
	    }
	    $perguntas = tarefa_checklist::select_1( $tipo );
	    foreach($perguntas as $pergunta)
	    {
		    $respostas = tarefa_checklist::select_2( $tarefas[0]['codigo'], $pergunta['cd_tarefa_checklist_pergunta'] );
		    if($respostas[0]['fl_resposta']=="")
		    {
				echo "
				    <script>
				    alert('Preencha antes o checklist de testes!');
				    location.href='frm_tarefa_checklist.php?os=$os&c=$c&f=$f';
				    </script>
				    ";
			    exit;
		    }
	    }
    }

	pg_query($db,"BEGIN TRANSACTION");

	$sql =	" UPDATE projetos.tarefas 
	             SET status_atual = 'LIBE',
				     dt_fim_prog  = current_timestamp
	           WHERE cd_tarefa    = ".$_REQUEST['t']."
			     AND cd_atividade = ".$_REQUEST['a'].";
		
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
					 ".$_REQUEST['t'].", 
					 ".$_REQUEST['a'].", 
					 ".$_REQUEST['recurso'].", 
					 current_timestamp, 
					 'Término da resolução da Tarefa.', 	
					 'LIBE',
					 '".pg_escape_string($_POST['motivo_tarefa'])."' 
		           );";	

	$ob_resul= @pg_query($db,$sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		pg_query($db,"ROLLBACK TRANSACTION");
		echo $ds_erro;
	}
	else
	{
		pg_query($db,"COMMIT TRANSACTION");
		$m = fnc_envia_email($_REQUEST['a'], $t, $db, $tpEmail);
		header('location: frm_tarefa.php?os='.$_REQUEST['a'].'&c='.$_REQUEST['t']);
	}				  
	pg_close($db);			
   
   
//--------------------------------- Envio de e-mails quando do cadastro de uma nova tarefa:
	function fnc_envia_email($cd_atividade, $cd_tarefa, $db, $tp) 
	{
		$sql = " SELECT t.cd_atividade,
                        t.cd_tarefa,		
		                u.guerra AS mandante, 
						t.descricao AS descricao, 
						t.programa AS programa, 
						u.usuario AS usuario
				   FROM projetos.tarefas t, 
				        projetos.usuarios_controledi u
				  WHERE	t.cd_atividade = ".$cd_atividade." 
				    AND t.cd_tarefa    = ".$cd_tarefa."
					AND t.cd_mandante  = u.codigo";
// -------------------------------------------------------------------------
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		$v_para = $reg['usuario']."@eletroceee.com.br";
		$v_cc = "";
		$v_de = "Controle de Atividades e Tarefas";
// ---------------------------------------------------------- Área da mensagem HTML:
		$v_assunto = "Conclusão da tarefa - nº ". $reg['cd_atividade'] . "/" . $reg['cd_tarefa'];	
		$v_msg = "Prezado(a) ".$reg['mandante']  . $vbcrlf;
		$v_msg = $v_msg . "A seguinte tarefa foi liberada pelo responsável pela execução:"  . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Tarefa: " . $reg['cd_tarefa']. ", Atividade: ". $reg['cd_atividade']  . $vbcrlf;
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
		$sql = $sql . "		current_timestamp, ";
		$sql = $sql . "		'$v_de', ";
		$sql = $sql . "		'$v_para', ";
		$sql = $sql . "		'$v_cc', ";
		$sql = $sql . "    	'$v_cco', ";
		$sql = $sql . "    	'" . str_replace("'", "`", $v_assunto) . "', ";
		$sql = $sql . "    	'" . str_replace("'", "`", $v_msg) . "' ";
		$sql = $sql . ")";	 
		//$sql = utf8_encode($sql);	
//	echo $sql;
		pg_query($db, $sql);
	}
?>