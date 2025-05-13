<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');
   $txt_dt_cadastro  = ( $data_implantacao  == '' ? 'Null' : "'".convdata_br_iso($data_implantacao)."'" );
   $txt_projeto  = ( $projeto  == '' ? 'Null' : $projeto);
	$sql =        " update projetos.programas ";
	$sql = $sql . " set ativo = '$opt_programa'	";
	$sql = $sql . " where programa = 'DAP Atendimento'			";
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: adm_atendimento.php');
	}
	else {
		pg_close($db);
		header('location: adm_atendimento.php?msg=Ocorreu um erro ao tentar gravar este programa.');
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