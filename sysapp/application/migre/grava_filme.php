<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$txt_dt_evento  = ( $dt_evento  == '' ? 'Null' : "'".convdata_br_iso($dt_evento)."'" );
	$ano_evento = substr($dt_evento, 6, 4);

	if ($ano_evento > '2003') {
		$v_diretorio = 'file://Srvmultimidia/MULTIMIDIA/Videos/'.$diretorio.'/';
	} else {
		$v_diretorio = 'file://Srvmultimidia/MULTIMIDIA1/Videos/'.$diretorio.'/';
	}
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update 	acs.videos ";
		$sql = $sql . " set 	titulo = '$titulo',  ";
		$sql = $sql . " 		local = '$local', ";
		$sql = $sql . " 		diretorio = '$v_diretorio', ";
		$sql = $sql . " 		arquivo = '$arquivo', ";
		$sql = $sql . " 		dt_evento = $txt_dt_evento, ";
		$sql = $sql . "     	dt_atualizacao = current_timestamp ";
		$sql = $sql . " where 	cd_video = $codigo         ";
	}
	else {
		$sql =        " insert 	into acs.videos ( ";
		$sql = $sql . "        	titulo , ";
		$sql = $sql . " 		local, ";
		$sql = $sql . " 		diretorio, ";
		$sql = $sql . " 		arquivo, ";
		$sql = $sql . " 		dt_evento, ";
		$sql = $sql . "        	dt_atualizacao ) ";
		$sql = $sql . " values 	( ";
		$sql = $sql . "        	'$titulo', ";
		$sql = $sql . "			'$local', ";
		$sql = $sql . "			'$v_diretorio', ";
		$sql = $sql . "			'$arquivo', ";
		$sql = $sql . "			$txt_dt_evento, ";
		$sql = $sql . "        	current_timestamp) ";
	}
// ------------------------------------------------
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_filmes.php'); 
	}
	else {
		pg_close($db);
		header('location: lst_filmes.php?msg=Ocorreu um erro ao tentar gravar o cargo.');
	}
// ------------------------------------------------
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