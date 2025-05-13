<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_telas_programas.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
	$tpl->prepare();
	$tpl->assign('n', $n);
// --------------------------------------------------------------------  
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('cd_software', $c);
	$tpl->newBlock('lista');
	$sql = "select 	cd_programa_fceee, cd_tela, nome_tela, descricao,  
	 				to_char(dt_cadastro, 'DD/MM/YYYY') as data_cad 
	 		from   	projetos.telas_programas 
	 		where 	cd_programa = '" . $c . "' order by nome_tela";
// --------------------------------------------------------------------  
	$rs = pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('telas');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('cd_software', $c);
		$tpl->assign('cd_tela',$reg['cd_tela']);
		$tpl->assign('nome_tela', $reg['nome_tela']);
		$tpl->assign('data_cad', $reg['data_cad']);
		$tpl->assign('descricao', $reg['descricao']);
		$sql2 = "select descricao from listas where codigo = '".$reg['cd_programa_fceee']."' and categoria = 'PRFC' ";
		$rs2 = pg_exec($db, $sql2);
		$reg2 = pg_fetch_array($rs2);
		$tpl->assign('cd_programa', $reg2['descricao']);
  	}
// --------------------------------------------------------------------  
	pg_close($db);
	$tpl->printToScreen();	
?>