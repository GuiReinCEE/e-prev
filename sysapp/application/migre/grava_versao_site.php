<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$txt_dt_inclusao  		= ( $dt_inclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_inclusao)."'" );
	$txt_dt_exclusao  		= ( $dt_exclusao  		== '' ? 'Null' : "'".convdata_br_iso($dt_exclusao)."'" );
	if (($cd_versao == '') and ($insere == 'I')) { $cd_versao = 1; }
	if ($insere=='I') {
		$sql =        " insert into projetos.root_site ( ";
		$sql = $sql . "       	cd_site, ";
		$sql = $sql . "       	cd_versao, ";
		$sql = $sql . "       	dt_versao, ";
		$sql = $sql . "       	dt_exclusao, ";
		$sql = $sql . "       	tit_capa, ";
		$sql = $sql . "       	destaque1, ";
		$sql = $sql . "       	destaque2, ";
		$sql = $sql . "       	destaque3, ";
		$sql = $sql . "       	link_destaque1, ";
		$sql = $sql . "       	link_destaque2, ";
		$sql = $sql . "       	link_destaque3, ";
		$sql = $sql . "       	texto_capa, ";
		$sql = $sql . "       	situacao, ";
		$sql = $sql . "       	endereco ) ";
		$sql = $sql . " values (					";
		$sql = $sql . "			$cd_site, ";
		$sql = $sql . "			$cd_versao, ";
		$sql = $sql . "			$txt_dt_inclusao, ";
		$sql = $sql . "			$txt_dt_exclusao, ";
		$sql = $sql . "			'$tit_capa', ";
		$sql = $sql . "       	'$destaque1', ";
		$sql = $sql . "       	'$destaque2', ";
		$sql = $sql . "       	'$destaque3', ";
		$sql = $sql . "       	'$link1', ";
		$sql = $sql . "       	'$link2', ";
		$sql = $sql . "       	'$link3', ";
		$sql = $sql . "			'$conteudo', ";
		$sql = $sql . "			'$situacao', ";
		$sql = $sql . "			'$endereco' ) ";
   }
   else {
		$sql =        " update projetos.root_site ";
		$sql = $sql . " set dt_versao = $txt_dt_inclusao, ";	  
		$sql = $sql . "     dt_exclusao = $txt_dt_exclusao, ";
		$sql = $sql . "		tit_capa = '$tit_capa', ";
		$sql = $sql . "		destaque1 = '$destaque1', ";
		$sql = $sql . "		destaque2 = '$destaque2', ";
		$sql = $sql . "		destaque3 = '$destaque3', ";
		$sql = $sql . "		link_destaque1 = '$link1', ";
		$sql = $sql . "		link_destaque2 = '$link2', ";
		$sql = $sql . "		link_destaque3 = '$link3', ";
		$sql = $sql . "		texto_capa = '$conteudo', ";
		$sql = $sql . "		situacao = '$situacao', ";
		$sql = $sql . "		endereco = '$endereco' ";
		$sql = $sql . " where cd_versao = $cd_versao and cd_site = $cd_site";
   }

	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_sites.php');
	}
	else {
		pg_close($db);
		header('location: lst_sites.php?msg=Ocorreu um erro ao tentar gravar este registro.');
	}
	
function convdata_br_iso($dt) {
      // Pressupѕe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL щ utilizando 
      // uma string no formato DDDD-MM-AA. Esta funчуo justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hora = date("H:m:s");
      return $a.'-'.$m.'-'.$d.' '.$hora;
   }

?>