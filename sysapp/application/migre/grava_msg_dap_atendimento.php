<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($txt_msg <> "") {
		$sql =        " update projetos.usuarios_controledi ";
		$sql = $sql . " set indic_msg = 'S',  ";
		$sql = $sql . "     texto_msg = '$txt_msg' ";
		$sql = $sql . " where divisao = 'GAP' and tipo not in ('X', 'P', 'D') ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: adm_atendimento.php');
	}
	else {
		pg_close($db);
		header('location: adm_atendimento.php?msg=Ocorreu um erro ao tentar gravar a Mensagem.');
	}
?>