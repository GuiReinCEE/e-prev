<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_adm_eleicoes.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------------------------------------	
	$sql =        " select 	ano_eleicao, cd_eleicao, nome ";
	$sql = $sql . " from   	eleicoes.eleicao ";
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('cidade');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('codigo', $reg['cd_eleicao']);
		$tpl->assign('ano', $reg['ano_eleicao']);
		$tpl->assign('nome', $reg['nome']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>