<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_macroregioes.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	{
		$sql =        " select cd_ibge, sigla_uf, nome_macroregiao ";
		$sql = $sql . " from expansao.macroregiao where cd_ibge = $c " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_ibge']);
		$tpl->assign('sigla', $reg['sigla_uf']);
		$tpl->assign('nome', $reg['nome_macroregiao']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>