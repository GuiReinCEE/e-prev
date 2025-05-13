<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$v_dt_etapa  = ( $dt_etapa  == '' ? 'Null' : "'".convdata_br_iso($dt_etapa)."'" );
	$v_etapa_anterior  = ( $etapa_anterior  == '' ? 0 : $etapa_anterior);
// ------------------------------------------------
	if ($cd_etapa<>"") {
		$sql =        " update 	projetos.etapas_projeto ";
		$sql = $sql . " set 	dt_etapa = $v_dt_etapa,  ";
		$sql = $sql . "     	usu_resp_etapa = $Z, ";
		$sql = $sql . "     	nome_etapa = '$nome', ";
		$sql = $sql . "     	etapa_anterior = $v_etapa_anterior, ";
		$sql = $sql . "     	situacao_etapa = '$cbo_etapa', ";
		$sql = $sql . "     	desc_etapa = '$descricao' ";
		$sql = $sql . " where 	cd_projeto = $codigo ";
		$sql = $sql . " and 	cd_etapa = $cd_etapa ";
	}
	else {
		$sql =        " insert into projetos.etapas_projeto ( ";
		$sql = $sql . "        cd_projeto , ";
		$sql = $sql . "        dt_etapa , ";
		$sql = $sql . "        usu_resp_etapa , ";
		$sql = $sql . "        nome_etapa , ";
		$sql = $sql . "        etapa_anterior , ";
		$sql = $sql . "        situacao_etapa , ";
		$sql = $sql . "        desc_etapa ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $codigo, ";
		$sql = $sql . "        $v_dt_etapa, ";
		$sql = $sql . "        $Z, ";
		$sql = $sql . "        '$nome', ";
		$sql = $sql . "        $v_etapa_anterior, ";
		$sql = $sql . "        '$cbo_etapa', ";
		$sql = $sql . "        '$descricao') ";
	}
// ------------------------------------------------
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_etapas_projeto.php?c='.$codigo);
	}
	else {
		pg_close($db);
		header('location: lst_etapas_projeto.php?c='.$codigo.'&msg=Ocorreu um erro ao tentar gravar este registro.');
	}
//------------------------------------------------------------------------
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