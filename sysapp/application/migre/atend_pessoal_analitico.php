<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

    header( 'location:'.base_url().'index.php/ecrm/atendimento_lista/atendente');

	$tpl = new TemplatePower('tpl/tpl_atend_pessoal_analitico.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('cor_filtro', $v_cor_fundo1);
// ---------------------------------------------------------
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$_REQUEST['dt_inicial'] = (trim($_REQUEST['dt_inicial']) == '' ? date('01/m/Y') : $_REQUEST['dt_inicial']);
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);
	
	$_REQUEST['dt_final']   = (trim($_REQUEST['dt_final'])   == '' ? date('d/m/Y') : $_REQUEST['dt_final']);
	$tpl->assign('dt_final', $_REQUEST['dt_final']);

	
	#### TIPO DE ATENDIMENTO ###
	switch ($_REQUEST['tipo_atendimento']) 
	{
		case 'C': $tpl->assign('sel_consulta', ' selected');
				  break;	
		case 'P': $tpl->assign('sel_pessoal', ' selected');
				  break;	
		case 'T': $tpl->assign('sel_telefonico', ' selected');
				  break;	
		case 'E': $tpl->assign('sel_email', ' selected');
				  break;	
		default: $tpl->assign('sel_todos', ' selected');
	}
	
	$tpl->newBlock('lista');
	
	$qr_sql = "
				SELECT a.id_atendente,
				       uc.guerra,
				       COUNT(*) AS qt_atendimento
				  FROM projetos.atendimento a, 
				       projetos.usuarios_controledi uc
				 WHERE a.id_atendente = uc.codigo 
				   --AND uc.tipo NOT IN('X', 'P')
				   --AND a.dt_hora_inicio_atendimento > '2004-07-04' 
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
				   AND DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
	          ";
	#### FILTRA TIPO ATENDIMENTO ####
	if ($_REQUEST['tipo_atendimento'] != '') 
	{ 
		$qr_sql.= "AND a.indic_ativo = '".$_REQUEST['tipo_atendimento']."'"; 
	}
	else	
	{
		$qr_sql.= "AND a.indic_ativo <> 'C'"; 
	}
	$qr_sql.= "
				 GROUP BY a.id_atendente, uc.guerra
				 ORDER BY qt_atendimento DESC		
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$nr_maior_atendimento = 0;
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('default');
		if (($nr_conta % 2) != 0) 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}		
		$tpl->assign('data', $ar_reg['guerra']);
		$tpl->assign('cd_atendente', $ar_reg['id_atendente']);
		$tpl->assign('numero_acessos_default', $ar_reg['qt_atendimento']);
		
		if($nr_conta == 0)
		{
			$nr_maior_atendimento = $ar_reg['qt_atendimento']; 
			$tpl->assign('numero_acessos_default_10', '100%');
		}
		else
		{
			$nr_tam = ($ar_reg['qt_atendimento'] * 100)/$nr_maior_atendimento;
			$tpl->assign('numero_acessos_default_10', $nr_tam.'%');
		}
		$nr_conta++;
	}

	$tpl->printToScreen();
	pg_close($db);
?>