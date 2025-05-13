<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	header( 'location:'.base_url().'index.php/cadastro/projeto' );

	$tpl = new TemplatePower('tpl/tpl_lst_projetos.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);	
	$tpl->newBlock('lista');

	//---------------------------------------------------------------------------
	
	$sql =        " SELECT DISTINCT codigo, nome, ";
	$sql = $sql . "                 descricao, ";
	$sql = $sql . "                 to_char(data_cad, 'DD/MM/YYYY') as data_cad, ";
	$sql = $sql . "                 to_char(data_implantacao, 'DD/MM/YYYY') as data_implantacao, ";
	$sql = $sql . "                 area ";
	$sql = $sql . " FROM projetos.projetos                 ";
	if ($T == 'D')
	{
		$sql = $sql . " where tipo = 'P' "; 
	}
	else 
	{
		$sql = $sql . " WHERE (area='$D' or atendente='$U' or analista_responsavel = '$U') and dt_exclusao is null and tipo = 'P' ";
	}
	$sql = $sql . " ORDER BY nome, area ";

	//---------------------------------------------------------------------------
	// echo $sql ;

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