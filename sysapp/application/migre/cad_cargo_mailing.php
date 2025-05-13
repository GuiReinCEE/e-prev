<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
//-------------------------------------------------------   
	$tpl = new TemplatePower('tpl/tpl_cad_cargos_mailing.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
	$tpl->assign('cd_mailing', $cd_mailing);
//-------------------------------------------------------
	if ($tr == 'U') {
		$n = 'U';
	}
	else {
		$n = 'I';
	}
	$tpl->assign('insere', $n);
	$tpl->assign('codigo', $c);
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
//-------------------------------------------------------
	if (isset($c))	{
        $sql = " select  cd_cargo, descricao ";
		$sql = $sql . "  from 	expansao.cargos_mailing	";
		$sql = $sql . "  where 	cd_cargo = $c ";
        $rs = pg_exec($db, $sql);
        $reg=pg_fetch_array($rs);
		$tpl->assign('descricao', $reg['descricao']);
   }
//-------------------------------------------------------
   pg_close($db);
   $tpl->printToScreen();	
?>