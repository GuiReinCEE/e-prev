<?
    include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');

	$qr_sql = " 
				UPDATE expansao.inscritos 
				   SET dt_documentacao_confirmada = CURRENT_TIMESTAMP
				 WHERE cd_empresa            = 7
				   AND cd_registro_empregado = ".$_REQUEST['cd_registro_empregado'];


	if (pg_query($db, $qr_sql)) 
	{
		pg_close($db);
		header('location: cad_inscritos_hist.php?c='.$_REQUEST['cd_registro_empregado'].'&a=h');
	}
	else {
		pg_close($db);
		header('location: cad_inscritos_hist.php?c='.$_REQUEST['cd_registro_empregado'].'&a=h&msg=Ocorreu um erro ao tentar gravar este registro.');
	}
?>