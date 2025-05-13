<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');

	$date = date("Y-m-d H:m:s");	
	$sql =		" update projetos.cenario ";
	$sql = $sql . " set arquivo_associado = ''";
	$sql = $sql . " where cd_cenario = $c ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_cenario.php?op=A&ed='.$ed.'&c='.$c);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
   }
 
?>