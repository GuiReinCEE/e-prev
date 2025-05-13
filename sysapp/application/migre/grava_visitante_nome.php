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
                   SET ds_nome = '{ds_nome}'
                     , nr_rg = {nr_rg}
                     , nr_cpf = {nr_cpf}
                     , cd_registro_empregado = {cd_registro_empregado}
                 WHERE cd_visitante IN ({cd_visitante})
            ");
            $dal->setAttribute( "{ds_nome}", $ds_nome[$index] );
            $dal->setAttribute( "{nr_rg}", $dal->ifBlankThen($nr_rg[$index], "null") );
            $dal->setAttribute( "{nr_cpf}", $dal->ifBlankThen($nr_cpf[$index], "null") );
            $dal->setAttribute( "{cd_registro_empregado}", $dal->ifBlankThen($cd_registro_empregado[$index], "null") );
            $dal->setAttribute( "{cd_visitante}", $cd_visitante[$index] );
            $result = $dal->executeQuery();
		}

    }
    
    //echo( $dal->getMessage() . "<br />" );
    
    header("Location: cad_visitante_manutencao_nome.php?pageIndex=".$_POST["pageIndex"]."&filtro_ds_nome=".$_POST["filtro_ds_nome"]."&filtro_ds_nome_prioridade=".$_POST["filtro_ds_nome_prioridade"]."");
?>