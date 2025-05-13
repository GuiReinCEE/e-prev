<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_update = "
					UPDATE projetos.enquete_resultados 
			           SET complemento = '".utf8_decode($_POST['new_value'])."'  
					 WHERE MD5(CAST(cd_enquete AS TEXT) || CAST(cd_agrupamento AS TEXT) || CAST(questao AS TEXT) || CAST(ip AS TEXT)) = '".$_POST['id']."'
				 ";
	$ob_resul = @pg_query($db,$qr_update);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		print json_encode(array("is_error" => true, "error_text"=> "Erro ao atualizar.", "html" => $_POST['orig_value']));
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 
		pg_close($db);
		print json_encode(array("is_error" => false, "error_text"=> "Erro ao atualizar.", "html" => $_POST['new_value']));
	}

?>