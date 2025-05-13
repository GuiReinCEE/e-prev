<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($ordem == '') {
		$ordem = 0;
	}
	if ($codigo <> "") {
		$sql =        " update projetos.enquete_respostas ";
		$sql = $sql . " set nome = '$resposta', ordem = $ordem  ";
		$sql = $sql . " where cd_enquete = $eq and cd_resposta = $codigo ";
	}
	else {
		$sql =        " insert into projetos.enquete_respostas ( ";
		$sql = $sql . "        cd_enquete , ";
		$sql = $sql . "        nome, ";
		$sql = $sql . "        ordem )";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $eq, ";
		$sql = $sql . "        '$resposta', ";
		$sql = $sql . "        $ordem ) ";
	}
// ------------------------------------------------
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_enquetes_estrutura.php?c='.$eq);
	}
	else {
		pg_close($db);
		header('location: cad_enquetes_estrutura.php?c='.$eq.'&msg=Ocorreu um erro ao tentar gravar esta resposta.');
	}
// ------------------------------------------------
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
?>