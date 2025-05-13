<?
header( "Content-Type: text/html; charset=iso-8859-1" );
include_once( 'inc/sessao.php' );
include_once( 'inc/conexao.php' );
include_once( 'inc/ePrev.Service.Projetos.php' );

class controle_projetos_avaliacao_config_partial_classificacao_usuario
{
    private $service;
    public $command;
    public $divisoes;
    public $matrizes;
    
    function __construct($db)
    {
        $this->service = new service_projetos($db);
        $this->requestParams();
        if($this->command=="ajax_salvar_classificacao")
        {
            $this->salvar();
        }

        $this->divisoes = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
        $this->matrizes = $this->service->familias_cargos__fetch_matriz_all();
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
        $usuario_matriz = new entity_projetos_usuario_matriz();
        $usuario_matriz->cd_usuario_matriz = 0;
        $usuario_matriz->cd_matriz_salarial = (int)$_POST['cd_matriz_salarial_hidden'];
        $usuario_matriz->cd_usuario = (int)$_POST['cd_usuario_hidden'];
        $usuario_matriz->dt_admissao = $_POST['dt_admissao_hidden'];
        $usuario_matriz->dt_promocao = $_POST['dt_promocao_hidden'];
        $usuario_matriz->cd_escolaridade = (int)$_POST['cd_escolaridade_hidden'];
        $usuario_matriz->tipo_promocao = $_POST['tipo_promocao_hidden'];
        
        $resposta = $this->service->usuarios_controledi__salvar_matriz( $usuario_matriz );
        
        if($resposta)
            echo 'true';
        else
            echo 'false';
    }
    private function consultar()
    {
        $matriz = $this->service->familias_cargos__fetch_matriz( (int)$_POST['classe_select'], $_POST['faixa_select'] );
        echo $matriz['cd_matriz_salarial'] . '|' . $matriz['valor_inicial'] . '|' . $matriz['valor_final'];
    }
    
    public function escolaridades($cd_usuario)
    {
    	$escolaridade = new hashtable_collection();
    	$escolaridade = $this->service->usuarios__escolaridades_por_usuario__get((int)$cd_usuario);
    	return $escolaridade;
    }
    
    public function selected($v1, $v2)
    {
    	if( $v1==$v2 ) echo 'SELECTED';
    }
}

$esta = new controle_projetos_avaliacao_config_partial_classificacao_usuario( $db );

if( $esta->command!='' )
{
    exit();
}

?>
<div id="message_panel"></div>
<CENTER>
<h2>Classificação de usuários</h2>
<input type='hidden' name='cd_matriz_salarial_hidden' id='cd_matriz_salarial_hidden' value='0' />
<input type='hidden' name='cd_usuario_hidden' id='cd_usuario_hidden' value='0' />
<input type='hidden' name='dt_admissao_hidden' id='dt_admissao_hidden' value='0' />
<input type='hidden' name='dt_promocao_hidden' id='dt_promocao_hidden' value='0' />
<input type='hidden' name='tipo_promocao_hidden' id='tipo_promocao_hidden' value='' />
<input type='hidden' name='cd_escolaridade_hidden' id='cd_escolaridade_hidden' value='0' />
<table border="0" cellpadding="0" cellspacing="0" class='fonte_padrao'>

    <? foreach( $esta->divisoes as $divisao ) : ?>

        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <b><?= $divisao->divisao; ?></b>
                <HR />
            </td>
        </tr>

        <tr>
            <td>
                <table>
                    <tr>
                        <td class='fonte_padrao'><b>Nome</b></td>
                        <td class='fonte_padrao'><b>Escolaridade</b></td>
                        <td class='fonte_padrao'><b>Classe - Faixa</b></td>
                        <td class='fonte_padrao'><b>Admissão</b></td>
                        <td class='fonte_padrao'><b>Promoção</b></td>
                        <td class='fonte_padrao'><b>Tipo</b></td>
                        <td></td>
                    </tr>
                    <? $bgcolor=''; ?>
                    <? $usuario = new entity_projetos_usuarios_controledi_extended(); ?>
                    <? foreach( $divisao->usuarios as $usuario ) : ?>
                        <? if ( $bgcolor=='' ) $bgcolor='#EEEEEE'; else $bgcolor=''; ?>
                        <tr bgcolor='<?= $bgcolor; ?>'>
                            <td class="fonte_padrao" nowrap style="width:220px;">
                            	<?= $usuario->get_nome(); ?>
                            </td>
                            <td>
                            	<? $item = new hashtable(); ?>
                            	<? $escolaridades = new hashtable_collection(); ?>
                            	<? $escolaridades = $esta->escolaridades( $usuario->get_codigo() ); ?>
                                <select 
                                    id='escolaridade_do_usuario_<?= $usuario->get_codigo(); ?>_select' 
                                    name='escolaridade_do_usuario_<?=$usuario->get_codigo();?>_select' 
                                    style='width:300px'
                                    >
                                    <option value='null'>:: selecione ::</option>
                                    <? foreach($escolaridades->items as $item): ?>
                                    	<option value='<?= $item->key ?>' <?= $esta->selected($item->key, $usuario->usuario_matriz->cd_escolaridade) ?>><?= $item->value ?></option>
                                    <? endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select 
                                    id='classe_do_usuario_<?= $usuario->get_codigo(); ?>_select' 
                                    name='classe_do_usuario_<?=$usuario->get_codigo();?>_select' 
                                    style='width:200px'
                                    >
                                    <option>:: selecione ::</option>
                                    <? foreach( $esta->matrizes as $matriz ) : ?>
                                        <option 
                                        <? if($matriz->cd_matriz_salarial == $usuario->usuario_matriz->cd_matriz_salarial) : ?>
                                            SELECTED
                                        <? endif; ?>
                                        value='<?= $matriz->cd_matriz_salarial ?>'>
                                        <?= $matriz->familias_cargos->get_classe() . ' - ' . $matriz->familias_cargos->get_nome_familia() . ' - faixa ' . $matriz->faixa ?></option>
                                    <? endforeach; ?>
                                </select>
                            </td>
                            <td><input id='dt_admissao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                name='dt_admissao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                style='width:100px;'
                                value='<?=$usuario->usuario_matriz->dt_admissao;?>'
                                ></td>
                            <td><input id='dt_promocao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                name='dt_promocao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                style='width:100px;'
                                value='<?=$usuario->usuario_matriz->dt_promocao;?>'
                                ></td>
                            <td>
                                <select
                                    id='tipo_promocao_<?= $usuario->get_codigo(); ?>_select'
                                    name='tipo_promocao_<?=$usuario->get_codigo();?>_select'
                                    >
                                    <option value=''>:: selecione ::</option>
                                    <option value='H'<? if($usuario->usuario_matriz->tipo_promocao == 'H') echo(" SELECTED"); ?>>Horizontal</option>
                                    <option value='V'<? if($usuario->usuario_matriz->tipo_promocao == 'V') echo(" SELECTED"); ?>>Vertical</option>
                                </select>
                            </td>
                            <td><input 
                                id='salvar_usuario_<?=$usuario->get_codigo();?>_button' 
                                name='salvar_usuario_<?=$usuario->get_codigo();?>_button' 
                                type='button' 
                                value='salvar'
                                usuarioId='<?=$usuario->get_codigo();?>'
                                matrizId='classe_do_usuario_<?=$usuario->get_codigo();?>_select'
                                admissaoId='dt_admissao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                promocaoId='dt_promocao_do_usuario_<?=$usuario->get_codigo();?>_text'
                                tipoPromocaoId='tipo_promocao_<?=$usuario->get_codigo();?>_select'
                                escolaridadeId='escolaridade_do_usuario_<?=$usuario->get_codigo();?>_select'
                                onclick='esta.salvar_usuario_Click( this )' 
                                /></td>
                        </tr>
                    <? endforeach; ?>
                </table>
            </td>
        </tr>

    <? endforeach; ?>

</table>

<BR>
<input type='button' name='salvar_button' value='Salvar' onclick='esta.salvar_matriz_Click()' class='botao' />
<BR><BR>
</CENTER>