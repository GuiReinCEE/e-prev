<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');
	include_once('inc/funcoes.php');
    include_once('inc/class.Email.inc.php');
	
    $cd_empresa = 7;
	$cd_plano = 7;
	$cd_sequencia = 0;
    
    $dal = new DBConnection();
    $dal->loadConnection( $db );
    
    $dal->createQuery("
        INSERT INTO projetos.envia_emails (
                dt_envio, 
                de, 
                para, 
                cc, 
                cco, 
                assunto, 
                texto, 
                cd_empresa, 
                cd_registro_empregado, 
                seq_dependencia,
                cd_evento
         )
         VALUES 
         (
                current_timestamp,
                '{remetente}',
                '{para}',
                '{cc}',
                '{cco}',
                '[Reenvio] {assunto}',
                '{texto}',
                {cd_empresa},
                {cd_registro_empregado},
                {seq_dependencia},
                {cd_evento}
         )
    ");

    $dal->setAttribute("{remetente}", $_POST["de"]);
    $dal->setAttribute("{para}", $_POST["para"]);
    $dal->setAttribute("{cc}", $_POST["cc"]);
    $dal->setAttribute("{cco}", $_POST["cco"]);
    $dal->setAttribute("{assunto}", $_POST["assunto"]);
    $dal->setAttribute("{texto}", $_POST["conteudo"]);

    $dal->setAttribute("{cd_empresa}", ($_POST["empresa"]!="")
                                                               ?$_POST["empresa"]
                                                               :"null"            );

    $dal->setAttribute("{cd_registro_empregado}", ($_POST["red"]!="")
                                                               ?$_POST["red"]
                                                               :"null"            );

    $dal->setAttribute("{seq_dependencia}", ($_POST["seq"]!="")
                                                               ?$_POST["seq"]
                                                               :"null"            );

    $dal->setAttribute("{cd_evento}", ($_POST["cd_evento"]!="")
                                                               ?$_POST["cd_evento"]
                                                               :"null"            );

    $dal->executeQuery();

	$dal = null;
    pg_close($db);
	
	if($e!='')
	{
		header( "location: lst_envia_emails.php?e=".$e );
	}
	else
	{
		header( "location: ".site_url('ecrm/emails_participante') );
	}
	
?>