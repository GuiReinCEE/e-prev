<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
// -----------------------------------------------------------------
	$sql =		" update projetos.divulgacao set arquivo_associado = null where cd_divulgacao = $c ";	
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_email_marketing.php?op=A&c='.$c);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
   }
// ----------------------------------------------------------------- 
?>