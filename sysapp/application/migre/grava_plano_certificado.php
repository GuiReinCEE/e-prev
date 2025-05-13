<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$txt_dt_aprovacao_spc = ( $dt_aprovacao_spc 	== '' ? 'Null' : "'".convdata_br_iso($dt_aprovacao_spc)."'" );
	$txt_dt_inicio = ( $dt_inicio 	== '' ? 'Null' : "'".convdata_br_iso($dt_inicio)."'" );
	$txt_dt_final = ( $dt_final 	== '' ? 'Null' : "'".convdata_br_iso($dt_final)."'" );
	$sql =        " update	planos_certificados ";
	$sql = $sql . " set 	nome_certificado = '$nome_certif', ";
	$sql = $sql . "     	cd_spc = '$cd_plano_spc', ";
	$sql = $sql . "     	pos_imagem = $posicao, ";
	$sql = $sql . "     	largura_imagem = $largura, ";
	$sql = $sql . "     	coluna_1 = '$coluna_1', ";
	$sql = $sql . "     	coluna_2 = '$coluna_2', ";
	$sql = $sql . "     	dt_inicio = $txt_dt_inicio, ";
	$sql = $sql . "     	dt_final = $txt_dt_final, ";
	$sql = $sql . "     	dt_aprovacao_spc = $txt_dt_aprovacao_spc ";
	$sql = $sql . " where 	cd_plano = $cd_plano and versao_certificado = $cd_versao";
//   echo "<br>$sql<br>";
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_planos.php?p='.$cd_plano);
	}
	else {
		pg_close($db);
		header('location: lst_planos.php?p='.$cd_plano.'&msg=Ocorreu um erro ao tentar gravar o plano.');
	}
   function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   }
?>