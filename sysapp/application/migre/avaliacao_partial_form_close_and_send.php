<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.DAL.DBConnection.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.avaliacao.php');

global $return_id;
global $return_message;
$return_id = 0;
$return_message = "";

if (isset($_POST["cd_avaliacao_selected_hidden"]))
{
    $cd_avaliacao = $_POST["cd_avaliacao_selected_hidden"];
}
else
{
    $cd_avaliacao = "";
}

if (!$cd_avaliacao=="")
{
    $entidade = new entity_projetos_avaliacao();
    $entidade->set_cd_avaliacao( $cd_avaliacao );
    $entidade->set_status( "F" );
    $entidade->set_dt_fechamento_avaliado( "CURRENT_TIMESTAMP" );
    
    $service = new service_projetos( $db );
    $rs = $service->avaliacao_UpdateCloseAndSend( $entidade );
    
    $service = null;
    $entidade = null;
}
if ( !$rs )
{
    pg_close($db);
    $return_id = 100;
    $return_message = "Ocorreu um erro ao salvar esta avaliação. {" . $sql . "}";
}
else
{
    $return_id = 1;
    $return_message = "- Avaliação atualizada.\n";
}

pg_close($db);

 /**
 * 
 * RETORNO DA PÁGINA:
 * 
 * $return_id
 *  1 - Todas as tabelas atualizadas
 *  2 - Algum erro ocorreu, mas alguma(s) tabela(s) foi(ram) atualizada(s)
 *  100 - Erro ao atualizar tabela projetos.avaliacao.
 * 
 * $return_message
 *  Mensagem de retorno, contém o andamento das atualizações ou erros que ocorreram.
 * 
 */
echo( $return_id );
echo( "|" );
echo( $return_message );
?>