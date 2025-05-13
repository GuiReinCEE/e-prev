<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes_edi.php');

	$nome_arquivo = $_FILES['arquivo']['name'];

	$nome_arquivo = ereg_replace("[����]","a",$nome_arquivo);
	$nome_arquivo = ereg_replace("[����]","A",$nome_arquivo);
	$nome_arquivo = ereg_replace("[���]","e",$nome_arquivo);
	$nome_arquivo = ereg_replace("[���]","E",$nome_arquivo);
	$nome_arquivo = ereg_replace("[�����]","o",$nome_arquivo);
	$nome_arquivo = ereg_replace("[����]","O",$nome_arquivo);
	$nome_arquivo = ereg_replace("[���]","u",$nome_arquivo);
	$nome_arquivo = ereg_replace("[���]","U",$nome_arquivo);
	$nome_arquivo = str_replace("�","c",$nome_arquivo);
	$nome_arquivo = str_replace("�","C",$nome_arquivo);
	$nome_arquivo = ereg_replace("-","_",$nome_arquivo); 
	$nome_arquivo = ereg_replace(" ","_",$nome_arquivo); 

	$nome_arquivo = rand(0, 999)."_".$nome_arquivo; 

	$sql = "
		SELECT COUNT(*) AS num_arquivos 
		FROM projetos.anexos_atividades 
		WHERE nome_arquivo = '$nome_arquivo' 
	";
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	if ($reg['num_arquivos'] > 0) 
	{
      	pg_close($db);
	  	header("location: cad_atividade_anexos.php?n=$cd_atividade&a=x");
   	}

	$uploadDir = '/u/www/upload/';
	$uploadFile = $uploadDir . $nome_arquivo;

   	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadFile))
   	{
      	$h = fopen($uploadFile, 'r');
		$sql 		= "select max(cd_anexo) as num_anexo from projetos.anexos_atividades ";
		$sql = $sql . " where cd_atividade = $cd_atividade ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$v_num_anexo = $reg['num_anexo'] + 1;

		$sql 		= "insert into projetos.anexos_atividades ( ";
		$sql = $sql . "       	cd_atividade, ";
		$sql = $sql . "       	cd_anexo, ";
		$sql = $sql . "       	tipo_anexo, ";
		$sql = $sql . "       	tam_arquivo, ";
		$sql = $sql . "       	dt_upload, ";
		$sql = $sql . "       	nome_arquivo, ";
		$sql = $sql . "       	caminho ) ";
		$sql = $sql . " values (					";
		$sql = $sql . "			$cd_atividade, ";
		$sql = $sql . "			$v_num_anexo, ";
		$sql = $sql . "			'".filetype($uploadFile)."', ";
		$sql = $sql . "			".filesize($uploadFile).", ";
		$sql = $sql . "			current_timestamp, ";
		$sql = $sql . "			'".$nome_arquivo."', ";
		$sql = $sql . "			'$uploadFile' ) ";
		pg_exec($db, $sql);
      	pg_close($db);
	  	header("location: cad_atividade_anexos.php?n=$cd_atividade&a=x");
   	}
   	else
   	{
		echo "<b>Ocorreu um erro ao tentar fazer o upload do arquivo!<br><br>Informa��es:</b>\n";
		print_r($_FILES);
		echo "</b>";
	}
?>