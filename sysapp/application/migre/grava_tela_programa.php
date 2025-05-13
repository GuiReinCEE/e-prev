<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   $txt_dt_cadastro  = ( $data_implantacao  == '' ? 'Null' : "'".convdata_br_iso($data_implantacao)."'" );
   $txt_projeto  = ( $projeto  == '' ? 'Null' : $projeto);
   if ($insere=='I') {
		$sql =        " insert into projetos.telas_programas ( ";
		$sql = $sql . "       	cd_programa, ";
		$sql = $sql . "       	nome_tela, ";
		$sql = $sql . "       	caption_tela, ";
		$sql = $sql . "       	cd_programa_fceee, ";
		$sql = $sql . "       	descricao ) ";
		$sql = $sql . " values ('$cod_programa',			";
		$sql = $sql . "			'$tela', ";
		$sql = $sql . "			'$caption',	";
		$sql = $sql . "			'$programa', ";
		$sql = $sql . "			'$descricao')	";
   }
   else {
		$sql =        " update projetos.telas_programas ";
		$sql = $sql . " set nome_tela = '$tela', ";
		$sql = $sql . "     caption_tela = '$caption', ";
		$sql = $sql . "     cd_programa_fceee = '$programa', ";	  
		$sql = $sql . "     descricao = '$descricao', ";
		$sql = $sql . "     dt_cadastro = $txt_dt_cadastro  ";
		$sql = $sql . " where cd_programa = '$cod_programa' and cd_tela = $cd_tela";
   }
	//echo $sql;exit;
	if ($rs=pg_query($db, $sql)) {
		pg_close($db);
		header('location: lst_telas_programas.php?c=' . $cod_programa);
	}
	else {
		pg_close($db);
		header('location: lst_telas_programas.php?c=' . $cod_programa . '&msg=Ocorreu um erro ao tentar gravar esta tela deste programa.');
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

?>