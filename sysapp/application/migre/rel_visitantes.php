<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php'); 
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_rel_visitantes.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$tpl->assign('ds_nome', $_POST['ds_nome']);
	$tpl->assign('dt_ini',  $_POST['dt_ini']);
	$tpl->assign('dt_fim',  $_POST['dt_fim']);

	$_POST['dt_ini'] = (trim($_POST['dt_ini']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_POST['dt_ini']."','DD/MM/YYYY')");
	$_POST['dt_fim'] = (trim($_POST['dt_fim']) == "" ? "CURRENT_DATE" : "TO_DATE('".$_POST['dt_fim']."','DD/MM/YYYY')");
	
	if(trim($_POST['ds_nome']) != "")
	{
		$qr_select = "					
						SELECT TO_CHAR(dt_entrada,'DD/MM/YYYY HH24:MM:SS') AS dt_entrada,
						       ds_destino,
						       dt_saida - dt_entrada AS hr_tempo
						  FROM projetos.visitantes
						 WHERE dt_entrada BETWEEN ".$_POST['dt_ini']." AND ".$_POST['dt_fim']."
						   AND UPPER(TRIM(ds_nome)) = UPPER('".trim(strtoupper($_POST['ds_nome']))."')
					 ";
		$ob_result = pg_query($db, $qr_select);	
		$nr_conta = 1;
		while($ar_reg = pg_fetch_array($ob_result))
		{
			$tpl->newBlock('lst_visitas');
			$tpl->assign('nr_conta',   $nr_conta);
			$tpl->assign('dt_data',    $ar_reg['dt_entrada']);
			$tpl->assign('ds_destino', $ar_reg['ds_destino']);
			$tpl->assign('hr_tempo',   $ar_reg['hr_tempo']);

			if(($nr_conta % 2) != 0)
			{
				$tpl->assign('bg_color', '#F4F4F4');
			}
			else
			{
				$tpl->assign('bg_color', '#FFFFFF');		
			}
			$nr_conta++;
		}	

	}
	
	$tpl->printToScreen();
	pg_close($db);
?>