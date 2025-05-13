<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
// ----------------------------------------------------------------------
	$date = date("Y-m-d H:m:s");	
// ----------------------------------------------------------------------
	$sql = "select count(distinct ip) as num_regs from projetos.enquete_resultados where cd_enquete = $cd_enquete and ip not like ('%.%')";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_object($rs);
	$nr_total_reg = $reg->num_regs;

	$sql =		" delete from projetos.usuarios_enquetes ";
	$sql = $sql . " where cd_enquete = $cd_enquete ";
	if (pg_exec($db, $sql)) {
	}
	else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
	}
// ----------------------------------------------------------------------
	$sql =		" delete from projetos.enquetes_participantes ";
	$sql = $sql . " where cd_enquete = $cd_enquete ";
	if (pg_exec($db, $sql)) {
	}
	else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
	}
// ----------------------------------------------------------------------
	$sql =		" insert into projetos.acao (tipo_acao, cd_responsavel, descricao, dt_acao) ";
	$sql = $sql . "		values ('E', ".$Z.", 'Exclusão de conteúdo de pesquisa: ".$cd_enquete." (total de ".$nr_total_reg." respostas excluidas)', current_timestamp) ";	
	if (pg_exec($db, $sql)) {
	}
	else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
	}
// ----------------------------------------------------------------------
	$sql =		" delete from projetos.enquete_resultados ";
	$sql = $sql . " where cd_enquete = $cd_enquete ";	
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_enquetes.php?c='.$cd_enquete);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este arquivo.";
   }
// ----------------------------------------------------------------------
?>