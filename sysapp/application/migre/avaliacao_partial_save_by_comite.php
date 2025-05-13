<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

global $return_id;
global $return_message;
$return_id = 0;
$return_message = "";

if(isset($_POST["ajax_command_hidden"]))
{
    $command = $_POST["ajax_command_hidden"];
}
else
{
    $command = "";
}

if (isset($_POST["grau_escolaridade"]))
{
    $grau_escolaridade = $_POST["grau_escolaridade"];
}
else
{
    $grau_escolaridade = "0";
}

$service = new service_projetos( $db );

// INCLUIR
$entidade = new entity_projetos_avaliacao_extended();

if($command=="insert_and_continue" || $command=="insert_and_send")
{
    // Insere na base
    $entidade->set_cd_avaliacao( "0" );
    $entidade->set_cd_usuario_avaliador( $_SESSION["Z"] );
    $entidade->set_tipo( "C" );
    $entidade->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );
    if($command=="insert_and_send")
    {
        $entidade->set_dt_conclusao( "CURRENT_TIMESTAMP" );
    }
    $rs = $service->avaliacao_Insert( $entidade );
    $cd_avaliacao = $entidade->get_cd_avaliacao();
}
else
{
    if($command=="save_and_send")
    {
        $entidade->set_cd_avaliacao( $_POST["cd_avaliacao_hidden"] );
        $entidade->set_dt_conclusao( "CURRENT_TIMESTAMP" );
        $rs = $service->avaliacao_Update( $entidade );
    }

    // Apenas recebe o post com código da avaliação e grava na variável
    $cd_avaliacao = $_POST["cd_avaliacao_hidden"];
}

$return_id = 1;
$return_message = "- Avaliação atualizada.\n";

// ENVIAR EMAIL AO SUPERIOR
$capa = new entity_projetos_avaliacao_capa_extended();
$capa->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );
$service->avaliacao_capa_envia_email_evento_34($capa, "PUBLICAR");

// Atualiza competências institucionais, competências específicas e responsabilidades
$sql = " DELETE FROM projetos.avaliacoes_comp_inst WHERE cd_avaliacao = " . (int)$cd_avaliacao . " ";
$s = (pg_query($db, $sql));

$v_comp_inst_informada = 0;

while( list($key, $value) = each($_POST) ) 
{ 
    $v_str = $key;
    if (strpos($v_str, "omp_inst") > 0)
    {
        if (is_numeric($value))
        {
            $v_comp_inst_informada = $v_comp_inst_informada + 1;
        }
        $m = fnc_grava_comp_inst($cd_avaliacao, str_replace('comp_inst', '', $v_str), $db, $value);
    }
}
pg_close($db);

/**
 * RETORNO DA PÁGINA:
 * 
 * $return_id
 *  1 - Todas as tabelas atualizadas
 *  2 - Algum erro ocorreu, mas alguma(s) tabela(s) foi(ram) atualizada(s)
 *  100 - Erro ao atualizar tabela projetos.avaliacao.
 * 
 * $return_message
 *  Mensagem de retorno, contém o andamento das atualizações ou erros que ocorreram.
 */
echo( $return_id );
echo( "|" );
echo( $return_message );

// ----------------------------------------------------------------------------------------------
function fnc_grava_comp_inst($cd_avaliacao, $cd_comp_inst, $db, $grau)
{
    global $return_id;
    global $return_message;
    if (is_numeric($grau))
    {
        $sql =          " INSERT INTO projetos.avaliacoes_comp_inst (";
        $sql = $sql .   "   cd_avaliacao, cd_comp_inst, grau ";
        $sql = $sql .   " ) ";
        $sql = $sql .   " VALUES ( ";
        $sql = $sql .   "   " . (int)$cd_avaliacao . ", " . (int)$cd_comp_inst . ", " . $grau . " ";
        $sql = $sql .   " ) ";
        $s = (pg_query($db, $sql));
        
        if (!$s)
        {
            $return_id = 2;
            $return_message .= "- Ocorreu um erro ao salvar Competências Institucionais. {" . $sql . "}\n";
        }
    }
    
    return $s;
}
?>