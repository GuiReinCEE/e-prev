<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

   	Header( 'Location:' . base_url() . 'index.php/atividade/tarefa' );

	if ($D != 'GI') 
	{
   		header('location: acesso_restrito.php?IMG=banner_exec_tarefa');
	}   

    $tpl = new TemplatePower('tpl/tpl_lst_minhas_tarefas.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	################################# DATAS #######################################
	$tpl->assign('dt_ini_encaminhado', $_REQUEST['dt_ini_encaminhado']);
	$tpl->assign('dt_fim_encaminhado', $_REQUEST['dt_fim_encaminhado']);
	$tpl->assign('dt_ini_concluido', $_REQUEST['dt_ini_concluido']);
	$tpl->assign('dt_fim_concluido', $_REQUEST['dt_fim_concluido']);
	$tpl->assign('cd_atividade', $_REQUEST['cd_atividade']);
	$tpl->assign('cd_tarefa', $_REQUEST['cd_tarefa']);
	

	$_REQUEST['dt_encaminhado'] = "";
	if(($_REQUEST['dt_ini_encaminhado'] != "") AND ($_REQUEST['dt_fim_encaminhado'] != ""))
	{
		$_REQUEST['dt_encaminhado'] = "TO_DATE('".$_REQUEST['dt_ini_encaminhado']."', 'DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_fim_encaminhado']."', 'DD/MM/YYYY')";
	}

	$_REQUEST['dt_concluido'] = "";
	if(($_REQUEST['dt_ini_concluido'] != "") AND ($_REQUEST['dt_fim_concluido'] != ""))
	{
		$_REQUEST['dt_concluido'] = "TO_DATE('".$_REQUEST['dt_ini_concluido']."', 'DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_fim_concluido']."', 'DD/MM/YYYY')";
	}
	
	################################# PRIORIDADE #######################################
	$tpl->assign('fl_prioridade_sim',$_REQUEST['cd_prioridade'] == "S" ? 'selected' : '');
	$tpl->assign('fl_prioridade_nao',$_REQUEST['cd_prioridade'] == "N" ? 'selected' : '');
	
	################################# STATUS #######################################
	if(count($_REQUEST['ar_status']) == 0)
	{
		$_REQUEST['ar_status'][] = 'AMAN';
		$_REQUEST['ar_status'][] = 'EMAN';
		$_REQUEST['ar_status'][] = 'SUSP';
		$_REQUEST['ar_status'][] = 'LIBE';
	}   

	$nr_conta = 0;
	while($nr_conta < count($_REQUEST['ar_status']))
	{
		$tpl->assign('fl_'.$_REQUEST['ar_status'][$nr_conta], 'checked');
		$nr_conta++;
	}
	
	#### LISTA TAREFAS ####
	$qr_sql = " 
				SELECT t.codigo AS codigo, 
	                   t.cd_atividade, 
					   t.cd_tarefa, 
					   t.descricao, 
					   t.casos_testes, 
					   t.cd_recurso,
                       ua.guerra AS ds_atendente,
					   t.cd_mandante,
					   us.guerra AS ds_solicitante,
	                   TO_CHAR(dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev, 
					   TO_CHAR(dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev, 
					   TO_CHAR(dt_inicio_prog, 'DD/MM/YYYY') AS dt_inicio_prog_ed, 
					   TO_CHAR(dt_fim_prog, 'DD/MM/YYYY') AS dt_fim_prog_ed,
					   TO_CHAR(dt_ok_anal, 'DD/MM/YYYY') AS dt_ok_anal_ed,
					   TO_CHAR(t.dt_encaminhamento, 'DD/MM/YYYY') AS dt_encaminhamento, 
					   CASE WHEN COALESCE(t.prioridade,'N') = 'S'
					        THEN '<b style=\"color:red\">Sim</b>'
							ELSE 'Não'
					   END AS prioridade, 
					   t.status_atual, 
					   t.resumo, 
					   t.fl_tarefa_tipo,
					   t.nr_nivel_prioridade
			      FROM projetos.tarefas t
				  JOIN projetos.usuarios_controledi ua
				    ON ua.codigo = t.cd_recurso
				  JOIN projetos.usuarios_controledi us
				    ON us.codigo = t.cd_mandante
	             WHERE t.dt_exclusao IS NULL 
				   AND ((t.cd_recurso = ".$_SESSION['Z']." AND t.dt_encaminhamento IS NOT NULL) OR t.cd_mandante = ".$_SESSION['Z'].") 
				   AND t.status_atual IN ('".implode("','", $_REQUEST['ar_status'])."')
				   ".($_REQUEST['cd_solicitante'] != "" ? " AND t.cd_mandante = ".$_REQUEST['cd_solicitante'] : "")."
				   ".($_REQUEST['cd_atendente']   != "" ? " AND t.cd_recurso = ".$_REQUEST['cd_atendente'] : "")."
				   ".($_REQUEST['cd_prioridade']  != "" ? " AND COALESCE(t.prioridade,'N') = '".$_REQUEST['cd_prioridade']."'" : "")."
				   ".($_REQUEST['dt_encaminhado'] != "" ? " AND DATE_TRUNC('day',t.dt_encaminhamento) BETWEEN ".$_REQUEST['dt_encaminhado'] : '')."
				   ".($_REQUEST['dt_concluido']   != "" ? " AND DATE_TRUNC('day',t.dt_ok_anal) BETWEEN ".$_REQUEST['dt_concluido'] : '')."
				   ".($_REQUEST['cd_atividade']   != "" ? " AND t.cd_atividade = ".intval($_REQUEST['cd_atividade']) : "")."
				   ".($_REQUEST['cd_tarefa']   != "" ? " AND t.cd_tarefa = ".intval($_REQUEST['cd_tarefa']) : "")."
				   
			     ORDER BY t.cd_atividade DESC 
			  ";

	$rs = pg_query($db, $qr_sql);
	$tpl->assign('qt_total_tarefas', pg_num_rows($rs));
	while ($reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('tarefas');
		$tpl->assign('evento', $reg['cd_tarefa']);
		$tpl->assign('cod_os', $reg['cd_atividade']);
		$tpl->assign('codtarefa', $reg['codigo']);
		$tpl->assign('resumo', $reg['resumo']);
		$tpl->assign('dt_fim_prog', $reg['dt_fim_prog_ed']);
		$tpl->assign('dt_fim_prev', $reg['dt_fim_prev']);
		$tpl->assign('dt_ok_anal', $reg['dt_ok_anal_ed']);
		$tpl->assign('dt_inicio_prev', $reg['dt_inicio_prev']);
		$tpl->assign('dt_encaminhamento', $reg['dt_encaminhamento']);
		$tpl->assign('dt_inicio_prog', $reg['dt_inicio_prog_ed']);
		$tpl->assign('fl_tipo_grava', strtolower($reg['fl_tarefa_tipo']));
		$tpl->assign('prioridade',$reg['prioridade']);
		$tpl->assign('solicitante', $reg['ds_solicitante']);
		$tpl->assign('executor', $reg['ds_atendente']);	
		$tpl->assign('nr_nivel_prioridade', $reg['nr_nivel_prioridade']);	
		
		switch($reg['status_atual'])
		{
			case 'AMAN' : 
				$status = 'Aguardando Manutenção'; 
				break;
			case 'EMAN' : 
				$status = 'Em Manutenção'; 
				break;
			case 'LIBE' : 
				$status = 'Liberada'; 
				break;
			case 'CONC' : 
				$status = 'Concluída'; 
				break;	
			case 'SUSP' : 
 				$status = 'Em Manutenção (Pausa)'; 
				break;	
		}
		$tpl->assign('status', $status);
	}	
		
		         
	################################# COMBO SOLICITANTE #######################################
		$sql = "SELECT codigo,
                       nome		
		          FROM projetos.usuarios_controledi 
				 WHERE tipo IN ('D','G','N','U')
				 ORDER BY nome";
			 
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_solicitante');
		$tpl->assign('cd_solicitante', '');
		$tpl->assign('ds_solicitante', 'Todos');
		$tpl->assign('fl_solicitante', ($_REQUEST['cd_solicitante'] == "" ? ' selected' : ''));
		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_solicitante');
			$tpl->assign('cd_solicitante', $reg['codigo']);
			$tpl->assign('ds_solicitante', $reg['nome']);
			$tpl->assign('fl_solicitante', ($reg['codigo'] == $_REQUEST['cd_solicitante'] ? ' selected' : ''));
		}		
		
	################################# COMBO ATENDENTE #######################################
		$sql = "SELECT codigo,
                       nome		
		          FROM projetos.usuarios_controledi 
				 WHERE tipo IN ('D','G','N','U', 'P')
				 ORDER BY nome";
			 
		$rs = pg_query($db, $sql);
		$tpl->newBlock('cbo_atendente');
		$tpl->assign('cd_atendente', '');
		$tpl->assign('cd_atividade', '');
		$tpl->assign('cd_tarefa', '');
		$tpl->assign('ds_atendente', 'Todos');
		$tpl->assign('fl_atendente', ($_REQUEST['cd_atendente'] == "" ? ' selected' : ''));
		
		while ($reg = pg_fetch_array($rs)) 
		{
			$tpl->newBlock('cbo_atendente');
			$tpl->assign('cd_atendente', $reg['codigo']);
			$tpl->assign('ds_atendente', $reg['nome']);
			$tpl->assign('fl_atendente', ($reg['codigo'] == $_REQUEST['cd_atendente'] ? ' selected' : ''));
		}	
	
	$tpl->printToscreen();
	pg_close($db);
?>