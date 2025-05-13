<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once('inc/sessao.php');
include_once('inc/conexao.php');

include_once('inc/ePrev.Service.Projetos.php');

global $return_id;
global $return_message;
$return_id = 0;
$return_message = "";

if (isset($_POST["grau_escolaridade"]))
{
    $grau_escolaridade = $_POST["grau_escolaridade"];
}
else
{
    $grau_escolaridade = "0";
}

if (isset($_POST["cd_avaliacao_hidden"]))
{
    $cd_avaliacao = $_POST["cd_avaliacao_hidden"];
}
else
{
    $cd_avaliacao = "";
}

if (isset($_POST["tipo_promocao_select"]))
{
    $tipo_promocao = $_POST["tipo_promocao_select"];
}
else
{
    $tipo_promocao = "";
}

$service = new service_projetos( $db );

// SALVAR CAPA (EDITAR)
$capa = new entity_projetos_avaliacao_capa_extended();
$capa->set_cd_avaliacao_capa(utf8_decode($_POST["cd_avaliacao_selected_hidden"]));
$capa->set_grau_escolaridade($grau_escolaridade);
$capa->set_tipo_promocao(utf8_decode($tipo_promocao));
$capa->set_status( $_POST["status_hidden"] );

$service->avaliacao_capa_Update($capa);

// SALVAR AVALIAÇÃO (INCLUIR OU EDITAR)
if ( !$cd_avaliacao=="" )
{
    // EDITAR
    $entidade = new entity_projetos_avaliacao_extended();
    $entidade->set_cd_avaliacao( $cd_avaliacao );

    $rs = $service->avaliacao_Update( $entidade );

    $entidade = null;
}
else
{
    // INCLUIR
    $entidade = new entity_projetos_avaliacao_extended();
    $entidade->set_cd_avaliacao( "0" );
    $entidade->set_cd_usuario_avaliador( $_POST["cd_usuario_avaliador_text"] );
    $entidade->set_tipo( "A" );
    $entidade->set_cd_avaliacao_capa( $_POST["cd_avaliacao_selected_hidden"] );
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

    if ($_POST["status_hidden"]=="F")
    {
        // ENVIAR EMAIL AO SUPERIOR
        $service->avaliacao_capa_envia_email_evento_34($capa, "SUPERIOR");
    }
}

// Atualiza competências institucionais, competências específicas e responsabilidades
$sql = " DELETE FROM projetos.avaliacoes_comp_inst WHERE cd_avaliacao = " . (int)$cd_avaliacao . " ";
$s = (pg_query($db, $sql));
$sql = " DELETE FROM projetos.avaliacoes_comp_espec WHERE cd_avaliacao = " . (int)$cd_avaliacao . " ";
$s = (pg_query($db, $sql));
$sql = " DELETE FROM projetos.avaliacoes_responsabilidades WHERE cd_avaliacao = " . (int)$cd_avaliacao . " ";
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
    if (strpos($v_str, "omp_espec") > 0)
    {
        $m = fnc_grava_comp_espec($cd_avaliacao, str_replace('comp_espec', '', $v_str), $db, $value);
    }
    if (strpos($v_str, "esponsabilidade") > 0)
    {
        $m = fnc_grava_responsabilidade($cd_avaliacao, str_replace('responsabilidade', '', $v_str), $db, $value);
    }
}

if ($_POST["status_hidden"]=="F")
{
	// Criar avaliação do avaliador a partir dessa.
	$ret = $service->avaliacao__avaliador__clone( $cd_avaliacao );
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

// ----------------------------------------------------------------------------------------------
function fnc_grava_comp_inst($cd_avaliacao, $cd_comp_inst, $db, $grau)
{
    global $return_id;
    global $return_message;
    if (is_numeric($grau))
    {
        $sql =          " INSERT INTO projetos.avaliacoes_comp_inst (";
        $sql = $sql .   " cd_avaliacao, cd_comp_inst, grau ";
        $sql = $sql .   " ) ";
        $sql = $sql .   " VALUES ( ";
        $sql = $sql .   " " . (int)$cd_avaliacao . ", " . (int)$cd_comp_inst . ", " . $grau . " ";
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

// ----------------------------------------------------------------------------------------------
function fnc_grava_comp_espec($cd_avaliacao, $cd_comp_espec, $db, $grau)
{
    global $return_id;
    global $return_message;
    if (is_numeric($grau))
    {
        $sql =          " INSERT INTO projetos.avaliacoes_comp_espec (";
        $sql = $sql .   " cd_avaliacao, cd_comp_espec, grau ";
        $sql = $sql .   " ) ";
        $sql = $sql .   " VALUES ( ";
        $sql = $sql .   " " . (int)$cd_avaliacao . ", " . (int)$cd_comp_espec . ", " . $grau . " ";
        $sql = $sql .   ")";
        $s = (pg_query($db, $sql));
        if (!$s)
        {
            $return_id = 2;
            $return_message .= "- Ocorreu um erro ao salvar Competências Específicas. {" . $sql . "}\n";
        }
    }

    return $s;
}

// ----------------------------------------------------------------------------------------------
function fnc_grava_responsabilidade($cd_avaliacao, $cd_responsabilidade, $db, $grau)
{
    global $return_id;
    global $return_message;
    if (is_numeric($grau)) {
        $sql =          " INSERT INTO projetos.avaliacoes_responsabilidades ( ";
        $sql = $sql .   " cd_avaliacao, cd_responsabilidade, grau ";
        $sql = $sql .   " ) ";
        $sql = $sql .   " VALUES ( ";
        $sql = $sql .   " " . (int)$cd_avaliacao . ", " . (int)$cd_responsabilidade . ", " . $grau . " ";
        $sql = $sql .   " ) ";
        $s = (pg_query($db, $sql));
        if (!$s)
        {
            $return_id = 2;
            $return_message .= "- Ocorreu um erro ao salvar Responsabilidades. {" . $sql . "}\n";
        }
    }
    
    return $s;
}
// ----------------------------------------------------------------------------------------------
?>