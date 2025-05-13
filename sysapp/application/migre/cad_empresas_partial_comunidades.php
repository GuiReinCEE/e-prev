<?
header( 'Content-Type: text/html; charset=iso-8859-1' );
include( 'inc/conexao.php' );
include( 'inc/ePrev.Service.Projetos.php' );

class controle_projetos_cad_empresas_partial_comunidades
{
    private $cd_empresa;
    private $cd_comunidade;
    private $cd_empresas_instituicoes_comunidades;
    private $service;
    public $comunidades;
    public $ajax_command;

    function __construct( $db )
    {
        $this->service = new service_projetos( $db );
        $this->requestParams();
        if($this->ajax_command=='ajax_insert_comunidade')
        {
            $this->adicionar_comunidade();
        }
        if($this->ajax_command=='ajax_delete_comunidade')
        {
            $this->remover_comunidade();
        }
    }

    function __destruct()
    {}

    private function requestParams()
    {
        if(isset($_POST['comando']))
        {
            $this->ajax_command = $_POST['comando'];
        }
        if(isset($_POST['codigo']))
        {
            $this->cd_empresa = $_POST['codigo'];
        }
        if(isset($_POST['cd_comunidade_text']))
        {
            $this->cd_comunidade = $_POST['cd_comunidade_text'];
        }
        if(isset($_POST['cd_empresas_instituicoes_comunidades_text']))
        {
            $this->cd_empresas_instituicoes_comunidades = $_POST['cd_empresas_instituicoes_comunidades_text'];
        }
    }

    public function listar_comunidades()
    {
        $this->comunidades = $this->service->expansao_empresas_instituicoes_comunidades_fetch_nao_incluidas($this->cd_empresa);
    }

    public function adicionar_comunidade()
    {
        $comunidade = new entity_expansao_empresas_instituicoes_comunidades();
        $comunidade->set_cd_emp_inst( $this->cd_empresa );
        $comunidade->set_cd_comunidade( $this->cd_comunidade );
        $this->service->expansao_empresas_instituicoes_comunidades_Insert( $comunidade );
    }
    public function remover_comunidade()
    {
        $comunidade = new entity_expansao_empresas_instituicoes_comunidades();
        $comunidade->set_cd_empresas_instituicoes_comunidades( $this->cd_empresas_instituicoes_comunidades );
        $this->service->expansao_empresas_instituicoes_comunidades_Delete( $comunidade );
    }

    public function listar_comunidades_da_empresa()
    {
        $this->comunidades_empresa = $this->service->expansao_empresas_instituicoes_comunidades_FetchAll($this->cd_empresa);
    }

    public function render_lista()
    {
        if( $this->comunidades_empresa )
        {
            echo('                            <table align="center" class="tb_lista_resultado">');
            echo('                                <tr>');
            echo('                                    <th class="td_border">Comunidade</th>');
            echo('                                    <th align="center" class="td_border"></th>');
            echo('                                </tr>');
            $bgcolor = "#FFFFFF";
            foreach( $this->comunidades_empresa as $empresa )
            {
                if($bgcolor!="#ffffff")
                {
                    $bgcolor="#ffffff";
                } 
                else
                { 
                    $bgcolor="#f4f4f4";
                }
                echo('                                    <tr bgcolor="' . $bgcolor . '">');
                echo('                                        <td class="td_border"><a href="lst_mailing.php?cac=CS3Y&sel=todos">' . $empresa->comunidade->get_descricao() . '</a></td>');
                echo('                                        <td align="center" class="td_border" style="width:10px"><a id="excluir_comunidade_link" href="javascript:void(0)" onclick="cad_empresas.excluir_comunidade_Click( '.$empresa->get_cd_empresas_instituicoes_comunidades().' )"><img src="img/btn_exclusao.jpg" border="0" /></a></td>');
                echo('                                    </tr>');
            }
            echo('                            </table>');
        }
    }
}

$this_page = new controle_projetos_cad_empresas_partial_comunidades( $db );
if($this_page->ajax_command!='')
{
    //unset( $this_page );
    //exit();
}

$this_page->listar_comunidades();
$this_page->listar_comunidades_da_empresa();
?>

<? if($this_page->comunidades){ ?>
    <br />
    <CENTER><b><span class="texto1">Comunidade:<span></b> <SELECT NAME="cd_comunidade_text" ID="cd_comunidade_text">
        <OPTION VALUE="">::Selecione::</OPTION>
        <? foreach( $this_page->comunidades as $comunidade ) { ?>
            <OPTION VALUE="<?= $comunidade->get_codigo()?>"><?=$comunidade->get_descricao() ?></OPTION>
        <? } ?>
    </SELECT>
    <INPUT NAME="adicionar_comunidade_button" ID="adicionar_comunidade_button" TYPE="button" VALUE="Adicionar" CLASS="botao" onclick="cad_empresas.adicionar_comunidade_Click();" /></CENTER>
<? } ?>
<br>
<center><div id="div_content_partial_comunidades" style="width:50%">
    <? $this_page->render_lista() ?>
</div></center>
<br><br><br>&nbsp
<? unset( $this_page ) ?>