<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
// --------------------------------------------------------------------------
	$sql =		" delete from projetos.projetos_envolvidos ";
	$sql = $sql . " where cd_projeto = $cd_projeto and cd_envolvido = $cd_envolvido ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_projetos.php?c='.$cd_projeto);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este envolvido.";
   } 
?>