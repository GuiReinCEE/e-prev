<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

header( 'Location:'.base_url().'index.php/home' );
exit;

$tpl = new TemplatePower('tpl/tpl_workspace_nova.html');
/*
	if(($_SESSION['Z'] == 170) or ($_SESSION['Z'] == 191))
	{
		$tpl = new TemplatePower('tpl/tpl_workspace_nova.html');
	}
	else
	{
		$tpl = new TemplatePower('tpl/tpl_workspace_1.html');
	}
*/
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);	
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	include_once('inc/skin.php');

	$sql = " SELECT count(*) as num_regs 
			   FROM	projetos.usuarios_enquetes ue, 
					projetos.enquetes e
			  WHERE	ue.cd_enquete        = 162 
				AND cd_usuario           = ".$_SESSION['Z']." 
				AND ue.cd_enquete        = e.cd_enquete 
				AND e.controle_respostas = 'U'";
	$rs = pg_query($db, $sql);
	$reg=pg_fetch_array($rs);
	if ($reg['num_regs'] == 0) 
	{
		$sql = " 
				SELECT CASE WHEN e.dt_fim < CURRENT_TIMESTAMP 
							THEN 'SIM'
							ELSE 'NAO'
					   END AS fl_encerrada
				  FROM projetos.enquetes e
				 WHERE e.cd_enquete = 162 			
			   ";
		$rs = pg_query($db, $sql);
		$reg=pg_fetch_array($rs);
		if ($reg['fl_encerrada'] == 'NAO') 
		{
			$tpl->newBlock('pesquisa_ti_2009');
		}
	}
	
	$tpl->printToScreen();

?>