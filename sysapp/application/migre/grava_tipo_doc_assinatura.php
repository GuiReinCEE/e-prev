<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update projetos.documentos_coordenadas    ";
		$sql = $sql . " set cd_tipo_doc = $cbo_tipo_doc,  ";
		$sql = $sql . "     x = $x, y = $y, altura = $altura, largura = $largura ";
		$sql = $sql . " where cd_tipo_doc = $cbo_tipo_doc         ";
	}
	else {
		$sql =        " insert into projetos.documentos_coordenadas ( ";
		$sql = $sql . "        cd_tipo_doc , ";
		$sql = $sql . "        x, y, altura, largura ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $cbo_tipo_doc, ";
		$sql = $sql . "        $x, $y, $altura, $largura) ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_tipos_docs_assinatura.php');
	}
	else {
		pg_close($db);
		header('location: lst_tipos_docs_assinatura.php?msg=Ocorreu um erro ao tentar gravar esta assinatura.');
	}
?>