<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_tipo_doc_assinatura.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('codigo', $c);
	if ($op=='I') {
		$tpl->newBlock('tipo_doc');
		$tpl->assign('cod_doc', '');
		$tpl->assign('nome_doc', '');
		$sql = "select cd_tipo_doc, nome_documento from tipo_documentos where cd_tipo_doc not in (select cd_tipo_doc from projetos.documentos_coordenadas) ";
		$rs = pg_exec($db, $sql);
		while ($reg = pg_fetch_array($rs)) {
			$tpl->newBlock('tipo_doc');
			$tpl->assign('cod_doc', $reg['cd_tipo_doc']);
			$tpl->assign('nome_doc', $reg['nome_documento']);
		}
	}
//-----------------------------------------------
	if (isset($c))	{
		$sql = "select cd_tipo_doc, nome_documento from tipo_documentos where cd_tipo_doc = $c ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$tpl->newBlock('tipo_doc_a');
		$tpl->assign('cod_doc', $reg['cd_tipo_doc']);
		$tpl->assign('nome_doc', $reg['nome_documento']);
		$tpl->newBlock('posicao');
		$sql =        " select x, y, altura, largura ";
		$sql = $sql . " from projetos.documentos_coordenadas where cd_tipo_doc = $c " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);		
		$tpl->assign('x', $reg['x']);
		$tpl->assign('y', $reg['y']);
		$tpl->assign('altura', $reg['altura']);
		$tpl->assign('largura', $reg['largura']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>