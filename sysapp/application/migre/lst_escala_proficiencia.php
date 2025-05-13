<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_escala_proficiencia.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$tpl->assign('origem', $origem);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------------------------------------	
	if (!gerencia_in(array('GAD'))) {
   		header('location: acesso_restrito.php?IMG=banner_escala_proficiencia');
	}
//--------------------------------------------------------------	
	if ($origem == 'CE') { $tpl->assign('escala', 'Competências Específicas'); }
	if ($origem == 'CI') { $tpl->assign('escala', 'Competências Institucionais'); }
	if ($origem == 'ES') { $tpl->assign('escala', 'Escolaridades'); }
	if ($origem == 'RE') { $tpl->assign('escala', 'Responsabilidades'); }
	$tpl->newBlock('lista');
	$sql =        " select 	cd_escala, descricao  ";
	$sql = $sql . " from   	projetos.escala_proficiencia ";
	$sql = $sql . " where 	cd_origem = '" . $origem ."' ";
	$sql = $sql . " and 	dt_exclusao is null ";
	$sql = $sql . " order 	by cd_escala" ;
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('grau');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('codigo', $reg['cd_escala']);
		$tpl->assign('descricao', $reg['descricao']);
		$tpl->assign('origem', $origem);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>