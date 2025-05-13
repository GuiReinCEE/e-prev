<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_tipos_docs_assinaturas.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------------------------------------	
	$tpl->newBlock('lista');
	$sql =        " select 	c.cd_tipo_doc, d.nome_documento, x, y, altura, largura ";
	$sql = $sql . " from   	projetos.documentos_coordenadas c, tipo_documentos d ";
	$sql = $sql . " where 	c.cd_tipo_doc = d.cd_tipo_doc ";
	$sql = $sql . " order 	by cd_tipo_doc, nome_documento ";	
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('documento');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('codigo', $reg['cd_tipo_doc']);
		$tpl->assign('nome_doc', $reg['nome_documento']);
		$tpl->assign('x', $reg['x']);
		$tpl->assign('y', $reg['y']);
		$tpl->assign('altura', $reg['altura']);
		$tpl->assign('largura', $reg['largura']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>