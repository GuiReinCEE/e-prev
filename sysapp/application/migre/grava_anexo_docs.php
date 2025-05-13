<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
//	include_once('inc/funcoes_edi.php');
//	$uploadDir = '/u/www/upload/';
//	$h = fopen($uploadFile, 'r');
//	echo $arq;
	if ($cd_link != '') {
		$sql 		= "update 	projetos.links_intra_div ";
		$sql = $sql . " set 	texto_link = '" . $texto_link ."', ";
		$sql = $sql . " 		link = '" . $arq . "' ";
		$sql = $sql . "	where 	cd_item = $cd_item  ";
		$sql = $sql . " and 	div = '$div' ";
		$sql = $sql . " and		cd_link = $cd_link ";
	}
	else {
		$sql 		= "insert into projetos.links_intra_div ";
		$sql = $sql . " (cd_item, ";
		$sql = $sql . " div, ";
		$sql = $sql . " texto_link, ";
		$sql = $sql . " dt_inclusao, ";
		$sql = $sql . " cd_usuario, ";
		$sql = $sql . " link, nr_ordem ";
		$sql = $sql . " ) values ( ";
		$sql = $sql . " $cd_item, ";
		$sql = $sql . " '$div', ";
		$sql = $sql . " '" . $texto_link . "', ";
		$sql = $sql . " current_timestamp, ";
		$sql = $sql . " $Z, ";
		$sql = $sql . "'".str_replace('//','/',str_replace('\\','/',$arq))."',
		               COALESCE((SELECT MAX(nr_ordem) + 1
		                  FROM projetos.links_intra_div 
                         WHERE cd_item = ".$cd_item."
						   AND div     = '".$div."'),0)) ";
	}
//	echo $sql;
	pg_exec($db, $sql);
  	pg_close($db);
  	header("location: documentos_anexos.php?i=$cd_item&d=".$div);
?>