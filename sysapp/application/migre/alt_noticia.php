<?PHP
	include_once("inc/sessao.php");
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$sql = "SELECT codigo, data, titulo, descricao, editorial FROM acs.noticias WHERE CODIGO=$cod";
	$rs = pg_exec($db, $sql);
	if ($r = pg_fetch_array($rs))
   {
		$tpl = new TemplatePower('tpl/tpl_cad_noticia.htm');
		$tpl->prepare();
// --------------------------------------------------------- inicialização do skin das telas:
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
		include_once('inc/skin.php');
// ---------------------------------------------------------
		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D);    
		$tpl->assign('codigo', $r['codigo']);
		$tpl->assign('titulo', $r['titulo']);
		$tpl->assign('noticia', $r['descricao']);
		switch ($r['editorial'])
		{
			case 'FP':$tpl->assign('sel_fp', ' selected'); break;
			 case 'FC':$tpl->assign('sel_fc', ' selected'); break;
			 case 'PR':$tpl->assign('sel_pr', ' selected'); break;
			 case 'CT':$tpl->assign('sel_ct', ' selected'); break;
			 case 'CO':$tpl->assign('sel_co', ' selected'); break;
			 case 'EA':$tpl->assign('sel_ea', ' selected'); break;
			 case 'EC':$tpl->assign('sel_ec', ' selected'); break;
			 case 'EN':$tpl->assign('sel_en', ' selected'); break;
			 case 'ET':$tpl->assign('sel_et', ' selected'); break;
			 case 'GE':$tpl->assign('sel_ge', ' selected'); break;
			 case 'PO':$tpl->assign('sel_po', ' selected'); break;
			 case 'QU':$tpl->assign('sel_qu', ' selected'); break;
			 case 'QV':$tpl->assign('sel_qv', ' selected'); break;
			 case 'RH':$tpl->assign('sel_rh', ' selected'); break;
		}
		$tpl->assign('pagina_acao', 'db_alt_noticia.php');
		$tpl->assign('lbl_botao', 'Alterar notícia');
		$tpl->printToScreen();
	}
	pg_close($db);
?>
