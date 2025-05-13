<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_escala_proficiencia.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	//if (($D <> 'GAD') and ($Z <> 110)) {
	if (!gerencia_in(array('GAD'))) {
   		header('location: acesso_restrito.php?IMG=banner_escala_proficiencia');
	}
//--------------------------------------------------------------	
	$tpl->newBlock('cadastro');
	$tpl->assign('origem', $origem);
	if (isset($c))	{
		$sql =        " select cd_origem, cd_escala, descricao ";
		$sql = $sql . " from projetos.escala_proficiencia where cd_escala='".$c."' and cd_origem ='".$origem."' ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_origem']);
		$tpl->assign('cd_escala', $reg['cd_escala']);
		$tpl->assign('descricao', $reg['descricao']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>