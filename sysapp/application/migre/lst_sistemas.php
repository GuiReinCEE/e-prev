<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location:'.site_url('cadastro/sistema') );
	
	include_once('inc/class.TemplatePower.inc.php');
//---------------------------------------------------------------------------
	$tpl = new TemplatePower('tpl/tpl_lst_sistemas.html');
	$tpl->prepare();
	$tpl->assign('n', $n);   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//---------------------------------------------------------------------------	
	$tpl->newBlock('lista');
	$sql =        " select distinct codigo, nome, ";
	$sql = $sql . "                 descricao, ";
	$sql = $sql . "                 to_char(data_cad, 'DD/MM/YYYY') as data_cad, ";
	$sql = $sql . "                 to_char(data_implantacao, 'DD/MM/YYYY') as data_implantacao, ";
	$sql = $sql . "                 area ";
	$sql = $sql . " from   projetos.projetos                 ";
	if (($Z == 110) or ($T == 'D')) {
		$sql = $sql . " where tipo = 'S' "; 
	}
	else {
		$sql = $sql . " where  area='$D' or atendente='$U' and dt_exclusao is null and tipo = 'S' ";
	}
	$sql = $sql . " order by area,nome                       ";
//---------------------------------------------------------------------------
//echo $sql ;
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('projetos');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('divisao',$reg['area']);
		$tpl->assign('cod_projeto', $reg['codigo']);
		$tpl->assign('projeto', $reg['nome']);
		$tpl->assign('descricao', $reg['descricao']);
		$tpl->assign('data_cad', $reg['data_cad']);
	}
	pg_close($db);
	$tpl->printToScreen();	
?>