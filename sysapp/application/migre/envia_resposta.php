<?
	include_once("inc/conexao.php");
	include_once("inc/class.Email.inc.php");
	include_once("inc/sessao.php");
    $cd_enter = chr(10).chr(13);
	
    $qr_sql = " 
				UPDATE contatos_internet
				   SET resposta       = '".p2nl(str_replace(chr(92),"",str_replace("'", "''", $_POST['resposta'])))."',
					   dt_resposta    = CURRENT_TIMESTAMP,
					   usuario        = '".$_SESSION['U']."',
					   cd_atendimento = '".$_POST['cbo_tipo_atendimento']."'
				 WHERE codigo = ".$_POST['codigo'].";
			  ";		 

	if ($_POST['fl_envia_email'] == "S")
	{
		$qr_sql.= " 			  
					INSERT INTO projetos.envia_emails 
						   ( 
							 dt_envio, 
							 de,
							 para, 
							 cc,
							 cco,
							 assunto,
							 texto 
						   ) 
					  VALUES
						   ( 
							 CURRENT_TIMESTAMP, 
							 'FUNDAÇÃO CEEE - Atendimento',
							 (SELECT TRIM(email) 
								FROM contatos_internet 
							   WHERE codigo = ".$_POST['codigo']."), 
							 '',
							 '',
							 'Resposta da sua sugestão [Contato nº ".$_POST['codigo']."]', 
							 'Prezada(o) ' || (SELECT TRIM(nome) 
												 FROM contatos_internet 
												WHERE codigo = ".$_POST['codigo'].")	
							  || '".$cd_enter."' || (SELECT TRIM(resposta) 
													   FROM contatos_internet 
													  WHERE codigo = ".$_POST['codigo'].")
						   );					 
					";
	}
	
	#echo "<PRE>$qr_sql</PRE>";exit;
   
   
	####  ABRE TRANSACAO COM O BD  ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul = @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		#echo $ds_erro;
		#exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 
	}   
	header("location: lista_contatos.php");
	
	function p2nl ($str) 
	{
	    return br2nl(preg_replace(array("/<p[^>]*>/iU","/<\/p[^>]*>/iU"),
	                        array("","\n"),
	                        $str));
	}	

	function br2nl($str) 
	{
	   return preg_replace('=<br */?>=i', "\n", $str);
	}
?>