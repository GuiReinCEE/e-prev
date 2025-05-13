<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_config_partial_conceitos
{
    private $db;
    public $command;
    function __construct($db)
    {
        $this->db = $db;
        $this->requestParams();
        if($this->command=="ajax_salvar_conceitos")
        {
            $this->salvar_conceitos();
        }
    }
    function __destruct()
    {
    }

    private function requestParams()
    {
        if(isset($_POST["ajax_command_hidden"]))
        {
            $this->command = $_POST["ajax_command_hidden"];
        }
    }

    public function texto_conceito( $codigo )
    {
        $service = new service_projetos($this->db);
        $e = new entity_public_listas();
        $e->set_codigo( $codigo );
        $service->public_listas_load_by_pk($e);
        return utf8_decode($e->get_descricao());
    }
    
    private function salvar_conceitos()
    {
        $service = new service_projetos($this->db);
        // varrer o formulário buscando os inputs de descrição e salvar um a um na base
        // considerando que os nomes são as PKs (listas.codigo)

        while( list($key, $value) = each($_POST) )
        {
            $codigo = explode('-', $key);
            if(sizeof($codigo)==2)
            {
                $entidade = new entity_public_listas();
                $entidade->set_codigo($codigo[1]);
                $entidade->set_descricao($value);
                $service->public_listas_alterar_descricao( $entidade );
            }
        }

        return true;
    }
}

$esta = new controle_projetos_avaliacao_config_partial_conceitos($db);
if($esta->command=="ajax_salvar_conceitos")
{
    exit();
}
?>
<div id="message_panel"></div>
<CENTER>
<h2>Competências Institucionais</h2>
<table border="0" cellpadding="0" cellspacing="0" class='fonte_padrao'>
    <tr>
        <td style='width:100px' align='center'><b>Conceito</b></td>
        <td><b>Descrição</b></td>
    </tr>
    <tr><td height='10'>&nbsp</td></tr>
    <tr>
        <td align='center'>A </td>
        <td><input type="text" id="CACI" name="codigo-CACI" value="<?=$esta->texto_conceito( 'CACI' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>B </td>
        <td><input type="text" id="CBCI" name="codigo-CBCI" value="<?=$esta->texto_conceito( 'CBCI' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>C </td>
        <td><input type="text" id="CCCI" name="codigo-CCCI" value="<?=$esta->texto_conceito( 'CCCI' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>D </td>
        <td><input type="text" id="CDCI" name="codigo-CDCI" value="<?=$esta->texto_conceito( 'CDCI' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>E </td>
        <td><input type="text" id="CECI" name="codigo-CECI" value="<?=$esta->texto_conceito( 'CECI' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>F </td>
        <td><input type="text" id="CFCI" name="codigo-CFCI" value="<?=$esta->texto_conceito( 'CFCI' )?>" style="width:500px" /></td>
    </tr>
</table>

<h2>Competências Específicas</h2>
<table border="0" cellpadding="0" cellspacing="0" class='fonte_padrao'>
    <tr>
        <td style='width:100px' align='center'><b>Conceito</b></td>
        <td><b>Descrição</b></td>
    </tr>
    <tr><td height='10'>&nbsp</td></tr>
    <tr>
        <td align='center'>A </td>
        <td><input type="text" id="CACE" name="codigo-CACE" value="<?=$esta->texto_conceito( 'CACE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>B </td>
        <td><input type="text" id="CBCE" name="codigo-CBCE" value="<?=$esta->texto_conceito( 'CBCE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>C </td>
        <td><input type="text" id="CCCE" name="codigo-CCCE" value="<?=$esta->texto_conceito( 'CCCE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>D </td>
        <td><input type="text" id="CDCE" name="codigo-CDCE" value="<?=$esta->texto_conceito( 'CDCE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>E </td>
        <td><input type="text" id="CECE" name="codigo-CECE" value="<?=$esta->texto_conceito( 'CECE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>F </td>
        <td><input type="text" id="CFCE" name="codigo-CFCE" value="<?=$esta->texto_conceito( 'CFCE' )?>" style="width:500px" /></td>
    </tr>
</table>

<h2>Responsabilidades</h2>
<table border="0" cellpadding="0" cellspacing="0" class='fonte_padrao'>
    <tr>
        <td style='width:100px' align='center'><b>Conceito</b></td>
        <td><b>Descrição</b></td>
    </tr>
    <tr><td height='10'>&nbsp</td></tr>
    <tr>
        <td align='center'>A </td>
        <td><input type="text" id="CARE" name="codigo-CARE" value="<?=$esta->texto_conceito( 'CARE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>B </td>
        <td><input type="text" id="CBRE" name="codigo-CBRE" value="<?=$esta->texto_conceito( 'CBRE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>C </td>
        <td><input type="text" id="CCRE" name="codigo-CCRE" value="<?=$esta->texto_conceito( 'CCRE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>D </td>
        <td><input type="text" id="CDRE" name="codigo-CDRE" value="<?=$esta->texto_conceito( 'CDRE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>E </td>
        <td><input type="text" id="CERE" name="codigo-CERE" value="<?=$esta->texto_conceito( 'CERE' )?>" style="width:500px" /></td>
    </tr>
    <tr>
        <td align='center'>F </td>
        <td><input type="text" id="CFRE" name="codigo-CFRE" value="<?=$esta->texto_conceito( 'CFRE' )?>" style="width:500px" /></td>
    </tr>
</table>
<BR>
<input type='button' name='salvar_button' value='Salvar' onclick='esta.salvar_Click()' class='botao' />
<BR><BR>
</CENTER>