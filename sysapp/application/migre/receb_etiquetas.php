<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$sql =        " select 	indic_07 ";
	$sql = $sql . " from   	projetos.usuarios_controledi ";
	$sql = $sql . " where 	codigo = $Z ";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if (($reg['indic_07'] == ' ') or ($reg['indic_07'] == '')) {
			header("location: acesso_restrito.php?IMG=banner_receb_votos"); 
		}
	}	 

	$tpl = new TemplatePower('tpl/tpl_receb_etiquetas.html');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	$tpl->newBlock('cadastro');
	$tpl->assign('usuario', $N);
//------------------------------------------------------------------------------------------- Combo avaliado
	$sql = "SELECT object_name, schema_name, row_count, object_size ";
	$sql = $sql . " FROM 	dba.dba_tam_objetos tao, projetos.tabelas_proprietarios tp ";
	$sql = $sql . " WHERE 	(tao.object_type = 'TABLE' or tao.object_type = 'VIEW')";
	$sql = $sql . " and    	(tp.cd_divisao = '" . $D . "' or tp.cd_divisao = 'FC')";
	$sql = $sql . " and		(tp.esquema = tao.schema_name) and (tp.tabela = tao.object_name)";
	$sql = $sql . " order by schema_name, object_name ";
	$tpl->newBlock('cbo_tabela');
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_tabela');
		$tpl->assign('cod_tabela', $reg['schema_name'] . "." . $reg['object_name']);
		$tpl->assign('nome_tabela', $reg['schema_name'] . "." . $reg['object_name']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>