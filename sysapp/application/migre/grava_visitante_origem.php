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
                   SET ds_origem = '{ds_origem}'
                 WHERE cd_visitante IN ({cd_visitante})
            ");
            $dal->setAttribute( "{ds_origem}", $ds_origem[$index] );
            $dal->setAttribute( "{cd_visitante}", $cd_visitante[$index] );
            $result = $dal->executeQuery();
		}

    }
    
    // echo( $dal->getMessage() . "<br />" );
    
    header("Location: cad_visitante_manutencao_procedencia.php?filtro_ds_origem=".$_POST["filtro_ds_origem"]."&filtro_ds_tipo=".$_POST["filtro_ds_tipo"]."");
?>