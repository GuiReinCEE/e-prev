<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes_edi.php');
   $uploadDir = '/u/www/upload/';
   $uploadFile = $uploadDir . $_FILES['arquivo']['name'];
   if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadFile))
   {
      	$h = fopen($uploadFile, 'r');
		$sql 		= "update projetos.cenario ";
		$sql = $sql . " set arquivo_associado = '".$_FILES['arquivo']['name']."' ";
		$sql = $sql . " where cd_cenario   = $cd_cenario ";
		pg_exec($db, $sql);
      	pg_close($db);
	  	header("location: cad_cenario.php?op=A&c=$cd_cenario&ed=$cd_edicao");
   }
   else
   {
      echo "<b>Ocorreu um erro ao tentar fazer o upload do arquivo!<br><br>Informações:</b>\n";
      print_r($_FILES);
	  echo "</b>";
   }
?>
?>