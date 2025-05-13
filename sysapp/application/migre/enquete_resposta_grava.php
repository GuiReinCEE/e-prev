<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_update = "
					UPDATE projetos.enquete_resultados 
			           SET descricao = '".utf8_decode($_POST['new_value'])."'  
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

/*
sleep( 3 );
$url		= $_POST['url'];
$form_type	= $_POST['form_type'];
$id			= $_POST['id'];
$orig_value	= $_POST['orig_value'];
$new_value	= $_POST['new_value'];

if( $form_type == 'select' ) 
{
	$orig_option_text	= $_POST['orig_option_text'];
	$new_option_text	= $_POST['new_option_text'];

	$new_value			= $new_option_text;
}


#print json_encode(array("is_error" => false, "error_text"=> "ERRO.", "html" => "teste"));


#url => http://10.63.255.150/cieprev/sysapp/application/migre/lst_respostas.php?cd_enquete=225
#id => 0548d47082e76756ad62f969e640e5e5
#form_type => textarea
#orig_value => Dizem respeito ao nosso futuro
#new_value => Dizem respeito futuro
#data => false
*/
?>