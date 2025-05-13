<?
//   include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$cd_empresa = 7;
	$cd_sequencia = 0;
	$txt_complemento_cep  = ( $complemento_cep  == '' ? 'Null' : "'".convdata_br_iso($complemento_cep)."'" );
	$txt_ramal  = ( $ramal  == '' ? 000 : $ramal );
	$txt_fax  = ( $fax  == '' ? 0 : $fax );
	$txt_celular  = ( $celular  == '' ? 0 : $celular );
	$txt_ddd_cel  = ( $ddd_cel  == '' ? 0 : $ddd_cel );
	$txt_ddd_fax  = ( $ddd_fax  == '' ? 0 : $ddd_fax );
// --------------------------------------------------------------------------------
	$sql =        " update 	expansao.inscritos ";
	$sql = $sql . " set 	endereco = '$endereco',	";
	$sql = $sql . "			bairro = '$bairro',	";
	$sql = $sql . "			uf = '$cbo_uf',	";
	$sql = $sql . "			cidade = '$cbo_cidade', ";		
	$sql = $sql . "			cep = $cep, ";
	$sql = $sql . "			complemento_cep = $complemento_cep, ";		
	$sql = $sql . "			ddd = $ddd, ";
	$sql = $sql . "			telefone = $telefone, ";
	$sql = $sql . "			ddd_cel = $txt_ddd_cel, ";
	$sql = $sql . "			celular = $txt_celular, ";
	$sql = $sql . "			ddd_fax = $txt_ddd_fax, ";
	$sql = $sql . "			fax = $txt_fax, ";
	$sql = $sql . "			ramal = $txt_ramal, ";
	$sql = $sql . "			email = '$email' ";
	$sql = $sql . " where 	cd_empresa = $cd_empresa ";
	$sql = $sql . "	  and	cd_registro_empregado = $cd_registro_empregado ";
// --------------------------------------------------------------------------------
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_inscritos_contato.php?c=' . $cd_registro_empregado . '&a=a');
	}
	else {
		pg_close($db);
		header('location: cad_inscritos_contato.php?c=' . $cd_registro_empregado . '&a=a&msg=Ocorreu um erro ao tentar gravar este registro.');
	}
// --------------------------------------------------------------------------------	
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