<?
	include_once('inc/conexao.php');
	include_once('inc/ePrev.DAL.DBConnection.php');

    $dal = new DBConnection();
    $dal->loadConnection($db);
    $cd_visitante = $_POST["cd_visitante"];
    for ( $index = 0; $index < sizeof($cd_visitante); $index++ ) {

        if ($changed[$index]=="s") {
            $dal->createQuery("
                UPDATE projetos.visitantes
                   SET ds_destino = '{ds_destino}'
                 WHERE cd_visitante IN ({cd_visitante})
            ");
            $dal->setAttribute( "{ds_destino}", $ds_destino[$index] );
            $dal->setAttribute( "{cd_visitante}", $cd_visitante[$index] );
            $result = $dal->executeQuery();
		}

    }
    
    //echo( $dal->getMessage() . "<br />" );
    
    header("Location: cad_visitante_manutencao_destino.php");
?>