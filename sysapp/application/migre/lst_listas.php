<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/servico/listas/index/'.$cat);
	
// -------------------------------------------------------------------
	$tpl = new TemplatePower('tpl/tpl_lst_listas.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// -------------------------------------------------------------------   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('cat',$cat);
// -------------------------------------------------------------------
	$sql =        " select codigo, descricao, divisao, valor, tipo, to_char(dt_exclusao, 'dd/mm/yyyy') as dt_exclusao ";
	$sql = $sql . " from   listas where categoria = '$cat' ";
	if ($v1 != '') {
		$sql = $sql . " and valor1 = '" . $v1 . "' ";
	}
	if ($v2 != '') {
		$sql = $sql . " and valor2 = '" . $v2 . "' ";
	}
	if ($div != '') {
		$sql = $sql . " and divisao = '" . $div . "' ";
	}
	if ($o == '') {
		$sql = $sql . " order by descricao "; }
	elseif ($o == 'C') {
		$sql = $sql . " order by codigo "; }
	elseif ($o == 'D') {
		$sql = $sql . " order by descricao "; }
	elseif ($o == 'I') {
		$sql = $sql . " order by divisao "; }
	elseif ($o == 'V') {
		$sql = $sql . " order by valor "; }
// -------------------------------------------------------------------
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('projetos');
		$cont = $cont + 1;
		if ($L == 'P') {
			$tpl->assign('cor_fundo',$v_cor_fundo1);
			$L = 'I';
		} else {
			$tpl->assign('cor_fundo',$v_cor_fundo2);
			$L = 'P';
		}
		$tpl->assign('cont',$cont);
		$tpl->assign('codigo',$reg['codigo']);
		$tpl->assign('desc',$reg['descricao']);
		$tpl->assign('div', $reg['divisao']);
		$tpl->assign('valor', $reg['valor']);
		$tpl->assign('tipo', $reg['tipo']);
		if ($reg['dt_exclusao'] != '') {
			$tpl->assign('cor_fundo',$v_cor_fundo4);
		}
		$tpl->assign('dt_exclusao', $reg['dt_exclusao']);
		$tpl->assign('cat',$cat);
	}
// -------------------------------------------------------------------
	$tpl->newBlock('total');
	$tpl->assign('total',$cont);
// -------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>