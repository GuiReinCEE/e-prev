<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class controle_projetos_avaliacao_config_partial_matriz_salarial
{
    private $service;
    public $command;
    public $familias;
    public $faixas;
    
    function __construct($db)
    {
        $this->service = new service_projetos($db);
        $this->requestParams();
        if($this->command=="ajax_salvar_matriz")
        {
            $this->salvar();
        }
        if($this->command=="ajax_consultar_matriz")
        {
            $this->consultar();
        }
        
        $this->familias = $this->service->familias_cargos__fetch_all();
        $this->faixas = $this->service->familias_cargos__fetch_faixas();
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
    
    private function salvar()
    {
        $matriz = array(
            
            'cd_matriz_salarial' => $_POST['cd_matriz_salarial_hidden'],
            'cd_familias_cargos' => $_POST['classe_select'],
            'faixa' => $_POST['faixa_select'],
            'valor_inicial' => $_POST['valor_incial_text'],
            'valor_final' => $_POST['valor_final_text']
            
        );
        $resposta = $this->service->familias_cargos__salvar_matriz($matriz);
        
        if($resposta) echo('true'); else echo('false');
    }
    private function consultar()
    {
        $matriz = $this->service->familias_cargos__fetch_matriz( $_POST['classe_select'], $_POST['faixa_select'] );
        echo $matriz['cd_matriz_salarial'] . '|' . $matriz['valor_inicial'] . '|' . $matriz['valor_final'];
    }
    
    public function get_valores( entity_projetos_familias_cargos $familia, entity_projetos_matriz_salarial $matriz )
    {
        $return = $this->service->familias_cargos__fetch_matriz( $familia->get_cd_familia(), $matriz->faixa );
        if($return['valor_inicial']!='' && $return['valor_final']!='')
        echo $return['valor_inicial'] . ' / ' . $return['valor_final'];
    }
}

$esta = new controle_projetos_avaliacao_config_partial_matriz_salarial($db);
if($esta->command!='')
{
    exit();
}
?>
<div id="message_panel"></div>
<CENTER>
<h2>Matriz Salarial</h2>
<input type='hidden' name='cd_matriz_salarial_hidden' id='cd_matriz_salarial_hidden' value='0' />
<table border="0" cellpadding="0" cellspacing="0" class='fonte_padrao'>
    <tr>
        <td width='150'>Informe a Classe: </td>
        <td>
            <select id='classe_select' name='classe_select' onchange='esta.consultar_matriz()' style='width:150px'>
                <option value=''>:: selecione ::</option>
                <? foreach($esta->familias as $familia) : ?>
                    <option value='<?= $familia->get_cd_familia() ?>'><?= $familia->get_classe() . ' - ' . $familia->get_nome_familia() ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    
    <tr>
        <td>Faixa:</td>
        <td>
            <select name='faixa_select' id='faixa_select' onchange='esta.consultar_matriz()' style='width:150px'>
                <option value=''>:: selecione ::</option>
                <option value='A'>A</option>
                <option value='B'>B</option>
                <option value='C'>C</option>
                <option value='D'>D</option>
                <option value='E'>E</option>
                <option value='F'>F</option>
                <option value='G'>G</option>
                <option value='H'>H</option>
                <option value='I'>I</option>
                <option value='J'>J</option>
                <option value='K'>K</option>
                <option value='L'>L</option>
            </select>
        </td>
    </tr>
    
    <tr>
        <td>Valor Inicial:</td>
        <td>
            <input 
                id='valor_inicial_text' 
                name='valor_incial_text' 
                value='0' 
                class="mask-numeric" 
                />
        </td>
    </tr>
    
    <tr>
        <td>Valor Final:</td>
        <td>
            <input 
                id='valor_final_text' 
                name='valor_final_text' 
                value='0'
                class="mask-numeric" 
            />
        </td>
    </tr>
</table>

<BR>
<input type='button' name='salvar_button' value='Salvar' onclick='esta.salvar_matriz_Click()' class='botao' />
<BR><BR>

<table class='fonte_padrao' width='100%' border='0'>

    <tr class='fonte_padrao'>
        <td bgcolor='0046ad' align='center' style='width:50px;font-size:12;color:white;'><b>Classe</b></td>
        <td bgcolor='0046ad' align='center' style='font-size:12;color:white;'><b>Faixa</b></td>
    </tr>

</table>
<table width='100%' class='fonte_padrao'>

    <tr class='fonte_padrao'>
        <td bgcolor='0046ad' style='width:50px;font-size:12;color:white;'></td>
        <? foreach( $esta->faixas as $faixa ) : ?>
            <td class='fonte_padrao' bgcolor='0046ad' align='center' style='color:white;'><b><?=$faixa->faixa;?></b></td>
        <? endforeach; ?>
    </tr>

    <?foreach($esta->familias as $familia) : ?>
        <tr>
            <td class='fonte_padrao' bgcolor='0046ad' style='width:50px;color:white;' align='center'><b><?=$familia->get_classe()?></b></td>

            <? foreach( $esta->faixas as $faixa ) : ?>

                <td class='fonte_padrao' align='center' bgcolor='#EEEEEE'><?$esta->get_valores( $familia, $faixa );?></td>

            <? endforeach; ?>

        </tr>
    <? endforeach; ?>

</table>

</CENTER>