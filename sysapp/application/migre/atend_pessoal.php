<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
    
    header( 'location:'.base_url().'index.php/ecrm/atendimento_lista/data');

	$tpl = new TemplatePower('tpl/tpl_atend_pessoal.html');
	$tpl->prepare();
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------
	$tpl->assign('n', $n);
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
				SELECT TO_CHAR(a.dt_hora_inicio_atendimento,'DD/MM/YYYY') AS dt_data,
				       DATE_TRUNC('day', a.dt_hora_inicio_atendimento) AS dt_data_ordem,
				       COUNT(*) AS qt_atendimento
				  FROM projetos.atendimento a
				 WHERE DATE_TRUNC('day',a.dt_hora_inicio_atendimento) BETWEEN TO_DATE('".$_REQUEST['dt_inicial']."','DD/MM/YYYY')  AND TO_DATE('".$_REQUEST['dt_final']."','DD/MM/YYYY')
				   AND (a.dt_hora_fim_atendimento - a.dt_hora_inicio_atendimento) > '00:00:10'::INTERVAL
	           ";
	#### FILTRA TIPO ATENDIMENTO ####
	if ($_REQUEST['tipo_atendimento'] != '') 
	{ 
		$qr_sql.= "AND a.indic_ativo = '".$_REQUEST['tipo_atendimento']."'"; 
	}	
	$qr_sql.= "
				 GROUP BY dt_data, dt_data_ordem
				 ORDER BY dt_data_ordem DESC
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 0;
	while ($reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('projetos');
		if (($nr_conta % 2) != 0) 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('data', $reg['dt_data']);
		$tpl->assign('numero_acessos', $reg['qt_atendimento']);
		
		$nr_conta++;
	}	
	
	
	$tpl->printToScreen();
	pg_close($db);

?>