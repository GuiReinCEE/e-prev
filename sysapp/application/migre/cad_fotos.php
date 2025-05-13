<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location: '.base_url().'index.php/ecrm/multimidia/foto_cadastro/'.intval($_REQUEST['cd_fotos']));
	EXIT;	
	
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_cad_fotos.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	if (($D <> 'GRI') and ($Z <> 191) and ($Z <> 170)) 
	{
   		header('location: acesso_restrito.php?IMG=banner_filmes');
	}
	
	$tpl->newBlock('cadastro');
	
	if (trim($_REQUEST['cd_fotos']) != "")	
	{
		$sql = " 
		        SELECT cd_fotos,
	                   ds_titulo, 
	                   TO_CHAR(dt_data, 'DD/MM/YYYY') AS dt_data,
	                   ds_caminho
	              FROM acs.fotos
				 WHERE cd_fotos = ".$_REQUEST['cd_fotos'];
		$ob_resul = pg_query($db, $sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$tpl->assign('cd_fotos',   $ar_reg['cd_fotos']);
		$tpl->assign('ds_titulo',  $ar_reg['ds_titulo']);
		$tpl->assign('dt_data',    $ar_reg['dt_data']);			
		$tpl->assign('ds_caminho', $ar_reg['ds_caminho']);
	}

	pg_close($db);
	$tpl->printToScreen();	
?>