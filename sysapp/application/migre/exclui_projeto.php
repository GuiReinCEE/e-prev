<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');

	$date = date("Y-m-d H:m:s");	
	$sql =		" update projetos.projetos ";
	$sql = $sql . " set dt_exclusao = '$date' ";
	$sql = $sql . " where codigo = $c ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_projetos.php');
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este projeto.";
   }
?>