<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
//   echo $insere;
   if ($insere=='I') {
		$sql =        " insert into acs.raiz_indicadores ( ";
		$sql = $sql . "       	cd_indic, ";
		$sql = $sql . "       	nome_indic, ";
		$sql = $sql . "       	ordem, ";
		$sql = $sql . "       	eixox, ";
		$sql = $sql . "       	eixoy, ";
		$sql = $sql . "       	num_series, ";
		$sql = $sql . "			seta, ";
		$sql = $sql . "			meta_raiz, ";
		$sql = $sql . "			img1, ";
		$sql = $sql . "			label1, ) ";
		$sql = $sql . "			img2, ";
		$sql = $sql . "			label2,  ";
		$sql = $sql . "			img3, ";
		$sql = $sql . "			label3,  ";
		$sql = $sql . "			img4, ";
		$sql = $sql . "			label4,  ";
		$sql = $sql . "			img5, ";
		$sql = $sql . "			label5,  ";
		$sql = $sql . "			tipo_grafico,  ";
		$sql = $sql . "			indic_meta, ";
		$sql = $sql . "			indic_rotulos ) ";
		$sql = $sql . " values (					";
		$sql = $sql . "			'$cd_indic', ";
		$sql = $sql . "			'$nome_indic', ";
		$sql = $sql . "			$ordem, ";
		$sql = $sql . "			'$eixox', ";
		$sql = $sql . "			'$eixoy', ";
		$sql = $sql . "			$num_series, ";
		$sql = $sql . "			'$seta', ";
		$sql = $sql . "			'$meta_raiz', ";
		$sql = $sql . "			'$imagem1', ";
		$sql = $sql . "			'$label1', ";
		$sql = $sql . "			'$imagem2', ";
		$sql = $sql . "			'$label2',  ";
		$sql = $sql . "			'$imagem3', ";
		$sql = $sql . "			'$label3',  ";
		$sql = $sql . "			'$imagem4', ";
		$sql = $sql . "			'$label4',  ";
		$sql = $sql . "			'$imagem5', ";
		$sql = $sql . "			'$label5',  ";
		$sql = $sql . "			'$tipo_grafico',  ";
		$sql = $sql . "			'$indic_meta', ";
		$sql = $sql . "			'$indic_rotulos' ) ";
   }
   else {
		$sql =        " update acs.raiz_indicadores ";
		$sql = $sql . " set nome_indic = '$nome_indic', ";
		$sql = $sql . "		ordem = $ordem, ";
		$sql = $sql . "		eixox = '$eixox', ";
		$sql = $sql . "		eixoy = '$eixoy', ";
		$sql = $sql . "		num_series = $num_series, ";
		$sql = $sql . " 	seta = '$seta', ";
		$sql = $sql . "		meta_raiz = '$meta_raiz', ";
		$sql = $sql . "		img1 = '$imagem1', ";
		$sql = $sql . "		label1 = '$label1', ";
		$sql = $sql . "		img2 = '$imagem2', ";
		$sql = $sql . "		label2 = '$label2', ";
		$sql = $sql . "		img3 = '$imagem3', ";
		$sql = $sql . "		label3 = '$label3', ";
		$sql = $sql . "		img4 = '$imagem4', ";
		$sql = $sql . "		label4 = '$label4', ";
		$sql = $sql . "		img5 = '$imagem5', ";
		$sql = $sql . "		label5 = '$label5', ";
		$sql = $sql . "		tipo_grafico = '$tipo_grafico', ";
		$sql = $sql . "		indic_meta = '$indic_meta', ";
		$sql = $sql . "		indic_rotulos = '$indic_rotulos' ";
		$sql = $sql . " where cd_indic = '$cd_indic' ";
   }
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_cad_indic_raiz.php');
	}
	else {
		pg_close($db);
		header('location: lst_indic_raiz.php?msg=Ocorreu um erro ao tentar gravar este indicador.');
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