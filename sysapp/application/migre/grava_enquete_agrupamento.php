<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo <> "") {
		$sql =        " update projetos.enquete_agrupamentos ";
		$sql = $sql . " set nome = '$agrupamento',  ";
		$sql = $sql . "   	indic_escala = '$opc_escala',  ";
		$sql = $sql . "   	ordem = $ordem,  ";
		$sql = $sql . "   	mostrar_valores = '$opc_valores',  ";
		$sql = $sql . "   	nota_rodape = '$nota_rodape',  ";
		$sql = $sql . "   	disposicao = '$opc_disposicao'  ";
		$sql = $sql . " where cd_enquete = $eq and cd_agrupamento = $codigo ";
	}
	else {
		$sql =        " insert into projetos.enquete_agrupamentos ( ";
		$sql = $sql . "        cd_enquete , ";
		$sql = $sql . "        nome, ";
		$sql = $sql . "        indic_escala, ";
		$sql = $sql . "        ordem, ";
		$sql = $sql . "        mostrar_valores, ";
		$sql = $sql . "        nota_rodape, ";
		$sql = $sql . "        disposicao) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $eq, ";
		$sql = $sql . "        '$agrupamento', ";
		$sql = $sql . "        '$opc_escala', ";
		$sql = $sql . "        $ordem, ";
		$sql = $sql . "        '$opc_valores', ";
		$sql = $sql . "        '$nota_rodape', ";
		$sql = $sql . "        '$opc_disposicao') ";
	}
// ------------------------------------------------
//	echo $sql;
	if ($rs=pg_query($db, $sql)) {
		pg_close($db);
		header('location: cad_enquetes_estrutura.php?c='.$eq);
	}
	else {
		pg_close($db);
		header('location: cad_enquetes_estrutura.php?c='.$eq.'&msg=Ocorreu um erro ao tentar gravar este agrupamento.');
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