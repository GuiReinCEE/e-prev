<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_cad_enquete_grupo.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	
	if(trim($_REQUEST['cd_enquete_grupo']) != "")
	{
		$ds_link = "http://".$_SERVER['SERVER_NAME']."/controle_projetos/enquete_inicio.php?c=".$_REQUEST['cd_enquete_grupo'];
		$qr_sql = "
					SELECT cd_enquete_sim,
						   cd_enquete_nao,
						   ds_titulo,
						   ds_pergunta
					  FROM projetos.enquete_grupo
					 WHERE cd_enquete_grupo = ".intval($_REQUEST['cd_enquete_grupo'])."
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		if($ob_resul)
		{
			$ar_reg = pg_fetch_array($ob_resul);
			$tpl->assign('cd_enquete_grupo', intval($_REQUEST['cd_enquete_grupo']));
			$tpl->assign('ds_titulo',      $ar_reg['ds_titulo']);
			$tpl->assign('ds_pergunta',    $ar_reg['ds_pergunta']);
			$tpl->assign('cd_enquete_sim', $ar_reg['cd_enquete_sim']);
			$tpl->assign('cd_enquete_nao', $ar_reg['cd_enquete_nao']);
			$tpl->assign('ds_link',        $ds_link);
		}
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>