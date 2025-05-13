<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_frm_exec_tarefa.html');
// ----------------------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	header( 'location:'.base_url().'index.php/atividade/tarefa_execucao/index/'.$os.'/'.$c);
	if ($D != 'GI')
	{
   		header('location: acesso_restrito.php?IMG=banner_exec_tarefa');
	}

	$tpl->newBlock('cadastro');
   	$tpl->assign('os', $os);
	$tpl->assign('origem', $os);
	$tpl->assign( "site_url", site_url());
	$tpl->assignGlobal('fl_tipo_grava', $f);
	$tpl->assign('historico', site_url('atividade/tarefa_historico/index/'.$os.'/'.$c));
	$tpl->assign('anexo', site_url('atividade/tarefa_anexo/index/'.$os.'/'.$c));
// ----------------------------------------------------------------------------
	if ($c != '') { // Tarefa criada para programador
		$sql =   " ";
		$sql = $sql . " select t.cd_atividade	as cd_atividade,   	 
                               t.cd_tarefa	as cd_tarefa, 
		                       t.cd_recurso as cd_recurso,
		                       t.programa as programa,
							   to_char(t.dt_inicio_prev,'dd/mm/yyyy') as dt_inicio_prev,
							   to_char(t.dt_fim_prev,'dd/mm/yyyy') as dt_fim_prev,
							   to_char(t.dt_hr_inicio,'dd/mm/yyyy hh:mi:ss') as dt_hora_inicio,
							   to_char(t.dt_hr_fim,'dd/mm/yyyy hh:mi:ss') as dt_hora_fim,
							   t.duracao as duracao,
							   t.descricao as descricao,
							   t.observacoes as observacoes,
							   t.casos_testes as casos_testes,
							   t.tabs_envolv as tabs_envolv,
							   t.imagem		as imagem,
							   to_char(t.hr_inicio,'hh:mi:ss') as hr_inicio,
							   to_char(t.dt_fim,'dd/mm/yyyy') as dt_fim,
							   to_char(t.hr_fim,'hh:mi:ss') as hr_fim,		
							   t.cd_mandante as cd_mandante,
							   t.cd_tipo_tarefa as cd_tipo_tarefa,
						       t.cd_classificacao as cd_classificacao,
							   to_char(t.dt_inicio_prog,'dd/mm/yyyy hh24:mi:ss') as dt_inicio_prog,
							   to_char(t.dt_fim_prog,'dd/mm/yyyy hh24:mi:ss') as dt_fim_prog,
							   to_char(t.dt_ok_anal,'dd/mm/yyyy hh24:mi:ss') as dt_ok_anal,
							   t.fl_checklist
		                  from projetos.tarefas t
		                 where t.cd_atividade = ".intval($os)."
		                   and t.cd_tarefa = ".intval($c);
		//echo "<PRE>".$sql; exit;
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
        
		$tpl->assign('codigo', $c);
		$tpl->assign('cd_tarefa', $reg['cd_tarefa']);
		$tpl->assign('dt_inicio', $reg['dt_inicio_prev']);
		$tpl->assign('dt_fim', $reg['dt_fim_prev']);
		$tpl->assign('tp_tarefa', $reg['tipo']);
		$tpl->assign('cd_habil', $reg['cd_habilidade']);
		$tpl->assign('descricao', str_replace(chr(13).chr(10), "<br>", $reg['descricao']));
		$tpl->assign('obs', $reg['observacoes']);
		$tpl->assign('dur_ant', $reg['duracao']);
		$tpl->assign('casos_testes', str_replace(chr(13).chr(10), "<br>", $reg['casos_testes']));
		$tpl->assign('tabs_envolv', str_replace(chr(13).chr(10), "<br>", $reg['tabs_envolv']));
        $tpl->assign('dt_cadastro',  $reg['dt_cadastro']);
        $tpl->assign('hr_inicio_real',  $reg['hr_inicio']);
        $tpl->assign('dt_fim_real',  $reg['dt_fim']);
        $tpl->assign('hr_fim_real',  $reg['hr_fim']);
		$tpl->assign('dt_ok_anal',  $reg['dt_ok_anal']);
		$tpl->assign('dt_fim_prog',  $reg['dt_fim_prog']);
		$tpl->assign('dt_inicio_prog',  $reg['dt_inicio_prog']);
		$tpl->assign('imagem',  $reg['imagem']);
		$tpl->assign('cd_recurso',  $reg['cd_recurso']);
		
		$cd_atividade = $reg['cd_atividade'];
		$v_cd_tarefa = $reg['cd_tarefa'];
		$v_cd_recurso = $reg['cd_recurso'];
		$v_cd_mandante = $reg['cd_mandante'];
		$v_programa = $reg['programa'];
		$cd_tipo_tarefa = $reg['cd_tipo_tarefa'];
		$cd_classificacao = $reg['cd_classificacao'];
		
		$tpl->assign('ver_checklist', ( $reg['fl_checklist']=='S' )?'display:;':'display:none;' );
	}

// ----------------------------------------------------- se � uma nova A��o, TR vem com 'I'
	if ($op == 'A') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	$tpl->assign('cd_tarefa', $c);
	$tpl->assign('insere', $n);
// ------------------------------------- Combo tarefa:
	$sql = "select cd_tarefa as cd_tarefa, 
                   nome_tarefa 
              from projetos.cad_tarefas 
             order by nome_tarefa";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('tarefa');
		$tpl->assign('cod_cad_tarefa', $reg['cd_tarefa']);
		$tpl->assign('nome_cad_tarefa', $reg['nome_tarefa']);
		
		if ($reg['cd_tarefa'] == $cd_tipo_tarefa) { 
			$tpl->assign('sel_tarefa', ' selected'); 
		}
	}

// ------------------------------------- Combo Analista:
	$sql = " 
				SELECT codigo AS cod_analista, 
				       nome 
	              from projetos.usuarios_controledi 
				 where tipo in('N','G') 
				   AND divisao='$S' 
				 order by nome 
			";
	$rs = pg_query($db, $sql);
   	$tpl->newBlock('mandante');
	$tpl->assign('cod_analista', '');
    $tpl->assign('nome_analista', 'Selecione');
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('mandante');
		$tpl->assign('cod_analista', $reg['cod_analista']);
		$tpl->assign('nome_analista', $reg['nome']);
		
		if ($reg['cod_analista'] == $v_cd_mandante) 
		{ 
			$tpl->assign('sel_analista', ' selected'); 
		}
	}
	
// ------------------------------------- Combo Programadores:
	$sql = " 
				SELECT codigo AS cod_atendente, 
				       nome
				  FROM projetos.usuarios_controledi 
				 WHERE (tipo <> 'X' OR codigo=".$v_cd_recurso." ) 
				   AND divisao='$S' 
				 ORDER BY nome 
		   ";
	$rs = pg_query($db, $sql);
	while ($reg=pg_fetch_array($rs)) 
	{
		$tpl->newBlock('atendente');
		$tpl->assign('cod_atendente', $reg['cod_atendente']);
		$tpl->assign('nome_atendente', $reg['nome']);
		if ($reg['cod_atendente'] == $v_cd_recurso) 
		{ 
			$tpl->assign('sel_atendente', ' selected'); 
		}
	}
// ------------------------------------- Combo Programa:
	$sql =        " select programa ";
	$sql = $sql . " from   projetos.programas ";
	$sql = $sql . " order by programa ";
	$rs = pg_exec($db, $sql);
	$tpl->newBlock('programa');
	$tpl->assign('programa', '');
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('programa');
		$tpl->assign('programa', $reg['programa']);
		if ($reg['programa'] == $v_programa) { $tpl->assign('sel_programa', ' selected'); }
	}
// ------------------------------------- Combo Classificacao:
	$sql = " select codigo as cd_classificacao,
                    descricao as ds_classificacao   
               from listas  
              where categoria = 'TTAR'
              order by descricao";
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('tipo_tarefa');
		$tpl->assign('cd_tipo_tarefa', $reg['cd_classificacao']);
		$tpl->assign('nome_tipo_tarefa', $reg['ds_classificacao']);
		
		if ($reg['cd_classificacao'] == $cd_classificacao) { 
			$tpl->assign('sel_tipo_tarefa', ' selected'); 
		}
	}
// ------------------------------------- Anexos de tarefas:
	/*
	if ($v_cd_tarefa != '') {
		$sql =        " select cd_anexo, tipo_anexo, caminho ";
		$sql = $sql . " from   projetos.anexos_tarefas ";
		$sql = $sql . " where  cd_tarefa = ".intval($v_cd_tarefa)." and cd_atividade = ".intval($cd_atividade)." ";
		$rs = pg_exec($db, $sql);
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('anexo');
			$tpl->assign('tipo_doc', $reg['tipo_anexo']);
			$tpl->assign('nome_doc', $reg['caminho']);
			if ($reg['tipo_tarefa'] == $v_tarefa) { $tpl->assign('sel_tipo_tarefa', ' selected'); }
		}
	}
	*/
//--------------------------------------------------MONTA BOTOES    

	$qr_historico = "
					SELECT status_atual AS fl_status
	                  FROM projetos.tarefa_historico
					 WHERE cd_atividade = ".$os."
		               AND cd_tarefa    = ".$c."
					   AND timestamp_alteracao =(SELECT MAX(timestamp_alteracao)
	                                               FROM projetos.tarefa_historico
					                              WHERE cd_atividade = ".intval($os)."
		                                            AND cd_tarefa    = ".intval($c)."
					                                AND cd_recurso   = ".intval($v_cd_recurso).")";
	$ob_dado = pg_exec($db, $qr_historico);
	$ar_dado = pg_fetch_array($ob_dado);
	$fl_status = $ar_dado['fl_status'];

	if($fl_status == "AMAN") 
	{
		$bt_tarefa_action = "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'PLAY')\"><img src='img/btn_play_vb.jpg' border='0'></a>";
		$bt_tarefa_action.= "<img src='img/btn_pause_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_stop_dis.jpg' border='0'>";			
	}	
	else if($fl_status == "EMAN") 
	{
		$bt_tarefa_action = "<img src='img/btn_play_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'PAUSE')\"><img src='img/btn_pause_vb.jpg' border='0'></a>";
		$bt_tarefa_action.= "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'STOP')\"><img src='img/btn_stop_vb.jpg' border='0'></a>";			
	}
	else if($fl_status == "SUSP") 
	{
		$bt_tarefa_action = "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'PLAY')\"><img src='img/btn_play_vb.jpg' border='0'></a>";
		$bt_tarefa_action.= "<img src='img/btn_pause_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'STOP')\"><img src='img/btn_stop_vb.jpg' border='0'></a>";
	}
	else if($fl_status == "LIBE") 
	{
		$bt_tarefa_action = "<img src='img/btn_play_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_pause_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_stop_dis.jpg' border='0'>";	
	}
	else if($fl_status == "CONC") 
	{
		$bt_tarefa_action = "<img src='img/btn_play_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_pause_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_stop_dis.jpg' border='0'>";	
	}
	else
	{
		$bt_tarefa_action = "<a href=\"javascript:showConfirma(".$cd_atividade.",".$v_cd_tarefa.",".$v_cd_recurso.",'PLAY')\"><img src='img/btn_play_vb.jpg' border='0'></a>";
		$bt_tarefa_action.= "<img src='img/btn_pause_dis.jpg' border='0'>";
		$bt_tarefa_action.= "<img src='img/btn_stop_dis.jpg' border='0'>";		
	}
	
	$tpl->newBlock('_bt_tarefa_action_');
	$tpl->assign('bt_tarefa_action', $bt_tarefa_action);
	//$tpl->assign('ds_status_atual', $fl_status);

	pg_close($db);
	$tpl->printToScreen();	
?>