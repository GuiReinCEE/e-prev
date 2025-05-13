<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_rel_prevenir_formulario.html');
	$tpl->prepare();
	$tpl->assign('n', $n);

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$_REQUEST['cd_pergunta'] = (trim($_REQUEST['cd_pergunta']) == "" ? 1 : $_REQUEST['cd_pergunta']);
	
	$tpl->assign('fl_pergunta_'.$_REQUEST['cd_pergunta'], 'class="abaSelecionada"');

	
	
	$qr_sql = "
				SELECT cd_pergunta,
				       o_que,
					   porque,
					   quem,
					   quando,
					   onde,
					   como
				  FROM prevenir.prevenir_formulario_item
				 WHERE dt_exclusao IS NULL
				   AND fl_exibir   = 'S'
				   AND cd_pergunta = ".$_REQUEST['cd_pergunta']."
			  ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assign('qt_registro', pg_num_rows($ob_resul));
	$tpl->newBlock('topo_pergunta_'.$_REQUEST['cd_pergunta']);
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		$tpl->newBlock('pergunta_'.$ar_reg['cd_pergunta']);
		$tpl->assign('o_que', $ar_reg['o_que']);
		$tpl->assign('porque', $ar_reg['porque']);				  
		$tpl->assign('quem', $ar_reg['quem']);				  
		$tpl->assign('quando', $ar_reg['quando']);				  
		$tpl->assign('onde', $ar_reg['onde']);				  
		$tpl->assign('como', $ar_reg['como']);				  
	}

	
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>