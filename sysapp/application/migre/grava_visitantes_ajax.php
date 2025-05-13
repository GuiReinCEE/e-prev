<?
	include_once('inc/conexao.php');
	
	//echo "<PRE>";
	//print_r($_POST);
	
	// ---> ABRE TRANSACAO COM O BD <--- //
	pg_query($db,"BEGIN TRANSACTION");	
		#### INSERT ####

		$qr_sql = "
					INSERT INTO projetos.visitantes
					     (
						   nr_cracha, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   nr_rg, 
						   nr_cpf, 
						   cd_tipo_visita,
						   ds_nome, 
						   ds_origem, 
						   ds_destino
						 )
                    VALUES 
					     (
						   ".trim((trim($_POST['nr_cracha']) == '' ? 'NULL' : $_POST['nr_cracha'])).", 
						   ".trim((trim($_POST['cd_emp'])    == '' ? 'NULL' : $_POST['cd_emp'])).", 
						   ".trim((trim($_POST['cd_re'])     == '' ? 'NULL' : $_POST['cd_re'])).", 
						   ".trim((trim($_POST['cd_seq'])     == '' ? 'NULL' : $_POST['cd_seq'])).", 
						   ".trim((trim($_POST['cd_rg'])     == '' ? 'NULL' : $_POST['cd_rg'])).", 						   
						   ".trim((trim($_POST['cd_cpf'])    == '' ? 'NULL' : $_POST['cd_cpf'])).", 						   						   
						   '".trim($_POST['cd_tipo_acesso'])."',
						   '".$_POST['ds_nome']."', 
						   UPPER('".trim($_POST['ds_origem'])."'), 
						   UPPER('".trim($_POST['ds_destino'])."')
                         )					
				  ";
	$ob_resul= @pg_query($db,utf8_decode($qr_sql));
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		// ---> DESFAZ A TRANSACAO COM BD<--- //
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		//echo "<pre>".$qr_sql;
		exit;
	}
	else
	{
		// ---> COMITA DADOS NO BD <--- //
		pg_query($db,"COMMIT TRANSACTION"); 		
		
		//header('location: frm_controle_acessso_pessoas.php');
	}
?>