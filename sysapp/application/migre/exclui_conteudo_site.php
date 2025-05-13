<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');

	$date = date("Y-m-d H:m:s");	
	$sql =		" update projetos.conteudo_site ";
	$sql = $sql . " set dt_exclusao = '$date' ";
	$sql = $sql . " where cd_materia = $c and cd_versao = $ed and cd_site = $cs ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_conteudo_site.php?op=A&c='.$c.'&ed='.$ed.'&cs='.$cs);
   }
   else {
      pg_close($db);
	  echo "Ocorreu um erro ao tentar excluir este conteudo.";
   }
 
   function convdata_br_iso($dt) {
      // Pressupѕe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL щ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
   function convtempo($hr) {
      // Pressupѕe que a data esteja no formato HH:MM:SS
      $h = substr($hr, 0, 2);
      $m = substr($hr, 3, 2);
      $s = substr($hr, 6, 2);
      return ($h * 3600) + ($m * 60) + $s;
   }
?>