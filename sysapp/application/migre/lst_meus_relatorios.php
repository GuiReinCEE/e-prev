<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_meus_relatorios.html');
	
	header( 'location:'.base_url().'index.php/servico/relatorio');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------------------------------------	
	$tpl->newBlock('lista');
	if ($ESPEC != '') {
		$sql =        " select 	cd_relatorio, esquema, tabela, to_char(dt_criacao, 'DD/MM/YYYY') as dt_criacao, titulo, divisao ";
		$sql = $sql . " from   	projetos.relatorios where especie = '" . $ESPEC ."'";
		$sql = $sql . " order 	by titulo ";
	}
	else {
		$sql =        " select 	cd_relatorio, esquema, tabela, to_char(dt_criacao, 'DD/MM/YYYY') as dt_criacao, titulo, divisao ";
		$sql = $sql . " from   	projetos.relatorios where cd_proprietario = $Z or divisao = '$D'";
		$sql = $sql . " order 	by esquema, tabela ";
	}
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('relatorio');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('esquema', $reg['esquema']);
		$tpl->assign('tabela', $reg['tabela']);
		$tpl->assign('dt_criacao', $reg['dt_criacao']);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('divisao', $reg['divisao']);
		$tpl->assign('cd_relatorio', $reg['cd_relatorio']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>