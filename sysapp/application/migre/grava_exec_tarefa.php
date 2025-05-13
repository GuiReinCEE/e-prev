<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
	
	$txt_dt_inicio_prev  = ( $dt_inicio  == '' ? 'Null' : "'".convdata_br_iso($dt_inicio)."'" );
	$txt_dt_fim_prev  = ( $dt_fim  == '' ? 'Null' : "'".convdata_br_iso($dt_fim)."'" );
	$txt_dt_deacordo  = ( $dt_deacordo  == '' ? 'Null' : "'".convdata_br_iso($dt_deacordo)."'" );
	$txt_dt_hr_inicio  = ( $dt_hr_inicio  == '' ? 'Null' : "'".convdata_br_iso($dt_hr_inicio)."'" );
	$txt_dt_hr_fim  = ( $dt_hr_fim  == '' ? 'Null' : "'".convdata_br_iso($dt_hr_fim)."'" );
	$txt_dt_inicio  = ( $dt_inicio_real  == '' ? 'Null' : "'".convdata_br_iso($dt_inicio_real)."'" );
	$txt_hr_inicio  = ( $hr_inicio_real  == '' ? 'Null' : "'".$hr_inicio_real."'" );
	$txt_dt_fim  = ( $dt_fim_real  == '' ? 'Null' : "'".convdata_br_iso($dt_fim_real)."'" );
	$txt_hr_fim  = ( $hr_fim_real  == '' ? 'Null' : "'".$hr_fim_real."'" );
	switch ($status) {
		case 'FECHADA': 	
			$txt_status = 'F';
			break;
		case 'EM PAUSA':
			$txt_status = 'P';
			break;
		case 'ABERTA':
			$txt_status = 'A';
			break;
		default: 
			$txt_status = '?';
	}		
	$v_duracao = (convtempo($duracao) + $dur_ant);
// ------------------------------------------------------------
//	if ($insere=='I') {
	if ($cd_tarefa == '') {
		$sql =        "insert into projetos.tarefas ( ";
	    $sql = $sql . "		cd_atividade, ";
		$sql = $sql . "		cd_recurso,	";
		$sql = $sql . "		programa, ";
		$sql = $sql . "		dt_inicio_prev, ";
		$sql = $sql . "		dt_fim_prev, ";
		$sql = $sql . "		dt_ok, ";
		$sql = $sql . "		dt_hr_inicio, ";
		$sql = $sql . "		dt_hr_fim, ";
		$sql = $sql . "		duracao, ";
		$sql = $sql . "		descricao, ";
		$sql = $sql . "		observacoes, ";
		$sql = $sql . "		casos_testes, ";
		$sql = $sql . "		tabs_envolv, ";
		$sql = $sql . "		dt_inicio, ";
		$sql = $sql . "		hr_inicio, ";
		$sql = $sql . "		dt_fim, ";
		$sql = $sql . "		hr_fim, ";
		$sql = $sql . "		cd_mandante	";
	    $sql = $sql . " ) ";
    	$sql = $sql . " VALUES ( ";
	    $sql = $sql . "		$origem, ";
	    $sql = $sql . "		$executor, ";
    	$sql = $sql . "		'$programa', ";	
	    $sql = $sql . "		$txt_dt_inicio_prev, ";	
	    $sql = $sql . "		$txt_dt_fim_prev, ";	
	    $sql = $sql . "		$txt_dt_deacordo, ";	
	    $sql = $sql . "		$txt_dt_hr_inicio, ";	
	    $sql = $sql . "		$txt_dt_hr_fim, ";	
	    $sql = $sql . "    	$v_duracao, ";
	    $sql = $sql . "    	'$descricao', ";
	    $sql = $sql . "    	'$obs', ";
	    $sql = $sql . "    	'$casos_testes', ";
	    $sql = $sql . "    	'$tabs_envolv', ";
	    $sql = $sql . "    	$txt_dt_inicio, ";
	    $sql = $sql . "    	$txt_hr_inicio, ";
	    $sql = $sql . "    	$txt_dt_fim, ";
	    $sql = $sql . "    	$txt_hr_fim, ";
	    $sql = $sql . "    	$mandante ";
    	$sql = $sql . ")";
	}
	else {
		$sql = " update projetos.tarefas 
                    set observacoes 	 = '$obs',
                        cd_tipo_tarefa   = $cad_tarefa,
                        cd_mandante      = $mandante,
                        cd_recurso       = $executor,
                        programa         = '$programa',
                        cd_classificacao = '$tipo_tarefa'   
                  where cd_atividade	 = $origem 
                    and	cd_tarefa		 = $cd_tarefa";

	}
		
	#echo "<PRE>".$sql;	exit;
			
	if (pg_exec($db, $sql)) {
//		if ($insere == 'I') {
		if ($cd_tarefa == '') {
			$sql =        "select   max(cd_tarefa) as cd_tarefa ";
			$sql = $sql . "from 	projetos.tarefas t ";
			$sql = $sql . "where 	t.cd_atividade = " . $origem ;
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$cd_tarefa = $reg['cd_tarefa'];
			$tpEmail = 'I';	
		}
		else {
			$tpEmail = 'A';
		}
		$m = fnc_envia_email($origem, $cd_tarefa, $db, $tpEmail);
		pg_close($db);
			header('location: frm_exec_tarefa.php?os='.$origem.'&c='.$cd_tarefa);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar incluir esta tarefa";
   }
 
   function convdata_br_iso($dt) {
      // PressupοΏ½e que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL οΏ½ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funοΏ½οΏ½o justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
   function convtempo($hr) {
      // PressupοΏ½e que a data esteja no formato HH:MM:SS
      $h = substr($hr, 0, 2);
      $m = substr($hr, 3, 2);
      $s = substr($hr, 6, 2);
      return ($h * 3600) + ($m * 60) + $s;
   }

//--------------------------------- Envio de e-mails quando do cadastro de uma nova tarefa:
	function fnc_envia_email($cd_atividade, $cd_tarefa, $db, $tp) {
//	echo $num_nao_conf;
		$e = new Email();
		$e->IsHTML();															
		$sql =        "select   t.cd_atividade, u.guerra as executor, t.descricao as descricao, t.programa as programa, u.usuario as usuario ";
		$sql = $sql . "from 	projetos.tarefas t, projetos.usuarios_controledi u ";
		$sql = $sql . "where 	t.cd_atividade = " . $cd_atividade . " and t.cd_tarefa = " . $cd_tarefa ;
		$sql = $sql . "and		t.cd_recurso = u.codigo ";

// -------------------------------------------------------------------------

		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$vbcrlf = chr(10).chr(13);
		$v_para = $reg['usuario']."@eletroceee.com.br";
		$v_cc = "";
		$v_de = "Controle de Atividades e Tarefas";
// ---------------------------------------------------------- Αrea da mensagem HTML:
		if ($tp == 'I') {
			$v_assunto = "Nova Tarefa solicitada - nοΏ½ ". $cd_atividade . "/" . $cd_tarefa;	
			$v_msg = "Prezado(a) ".$reg['executor']  . $vbcrlf;
			$v_msg = $v_msg . "Uma nova Tarefa foi solicitada:"  . $vbcrlf;
		}
		else { 
			$v_assunto = "Houve uma atualizaηγo na Tarefa - nϊm ". $cd_atividade . "/" . $cd_tarefa;	
			$v_msg = "Prezado(a) ".$reg['executor']  . $vbcrlf;
			$v_msg = $v_msg . "Atualizaηγo da tarefa encaminhada:"  . $vbcrlf;
		}
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "Tarefa: " . $num_tarefa. ", Atividade: ". $reg['cd_atividade']  . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
// ----------------------------------------- descriοηγo da tarefa:
		$v_msg = $v_msg . "Descriοηγo: " . $reg['descricao']  . $vbcrlf;
		$v_msg = $v_msg . "-------------------------------------------------------------" . $vbcrlf;
		$v_msg = $v_msg . "A presente tarefa deve ser iniciada imediatamente!"  . $vbcrlf;
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