<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_escolaridade.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	/*if (($D <> 'GAD') and ($Z <> 110)) {
   		header('location: acesso_restrito.php?IMG=banner_escolaridade');
	}*/
//--------------------------------------------------------------	
		$tpl->newBlock('cadastro');
		if (isset($c))	{
			$sql =        " select cd_escolaridade, nome_escolaridade, desc_escolaridade ";
			$sql = $sql . " from projetos.escolaridade where cd_escolaridade=$c " ;
			$rs = pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			$tpl->assign('codigo', $reg['cd_escolaridade']);
			$tpl->assign('nome', $reg['nome_escolaridade']);
			$tpl->assign('descricao', $reg['desc_escolaridade']);
		}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>