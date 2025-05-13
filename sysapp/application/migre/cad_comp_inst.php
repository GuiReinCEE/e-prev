<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_comp_inst.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	if (!gerencia_in(array('GAP'))) {
   		header('location: acesso_restrito.php?IMG=banner_comp_espec');
	}
//--------------------------------------------------------------	
		$tpl->newBlock('cadastro');
		if (isset($c))	{
			$sql =        " select cd_comp_inst, nome_comp_inst, desc_comp_inst ";
			$sql = $sql . " from projetos.comp_inst where cd_comp_inst=$c " ;
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('codigo', $reg['cd_comp_inst']);
			$tpl->assign('nome', $reg['nome_comp_inst']);
			$tpl->assign('descricao', $reg['desc_comp_inst']);
		}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>