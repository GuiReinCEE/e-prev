<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_envolvidos_projeto.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);

	if (isset($cd_envolvido) and isset($cd_projeto))	{
		$sql =        " select 	u.guerra, p.nome ";
		$sql = $sql . " from 	projetos.projetos_envolvidos e, projetos.usuarios_controledi u, projetos.projetos p ";
		$sql = $sql . " where 	e.cd_projeto = $cd_projeto" ;
		$sql = $sql . " and 	e.cd_envolvido = $cd_envolvido ";
		$sql = $sql . " and		e.cd_projeto = p.codigo ";
		$sql = $sql . " and		e.cd_envolvido = u.codigo and u.tipo not in ('X', 'P', 'T')";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_projeto', $cd_projeto);
		$tpl->assign('cd_envolvido', $cd_envolvido);
		$tpl->assign('projeto', $reg['nome']);
		$tpl->assign('envolvido', $reg['guerra']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>