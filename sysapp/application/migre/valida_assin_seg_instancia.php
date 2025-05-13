<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
//	if (($Z != 110) and ($Z != 94)) { // retirar apуs testar
//		header("location: manutencao.php?IMG=banner_receb_votos"); 
//	}
// --------------------------------------------------- Responsбvel pela aceitaзгo em segunda instвncia
	$sql =        " select 	indic_07 ";
	$sql = $sql . " from   	projetos.usuarios_controledi ";
	$sql = $sql . " where 	codigo = $Z ";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if ($reg['indic_07'] != 'S') {
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