<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$sql = " SELECT indic_07 ";
	$sql = $sql . " FROM projetos.usuarios_controledi ";
	$sql = $sql . " WHERE codigo = $Z ";
	$rs = pg_query($db, $sql);

	if ($reg=pg_fetch_array($rs)) 
	{
		if (($reg['indic_07'] == ' ') or ($reg['indic_07'] == '')) {
			header("location: acesso_restrito.php?IMG=banner_receb_votos"); 
		}
	}

	$tpl = new TemplatePower('tpl/tpl_valida_assinatura.html');
	
	//-----------------------------------------------
	   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	//-----------------------------------------------

	$tpl->newBlock('cadastro');
	$tpl->assign('usuario', $N);
	
	//-----------------------------------------------
	
	pg_close($db);
	$tpl->printToScreen();	
?>