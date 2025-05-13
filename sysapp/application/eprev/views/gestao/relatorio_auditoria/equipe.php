<?php
set_title('Relatório Auditoria');
$this->load->view('header');
?>
<script>
    <?php
		echo form_default_js_submit(Array('cd_usuario', 'tipo'));
    ?>
    function ir_lista()
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria"); ?>';
    }
    
    function ir_cadastro(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/cadastro"); ?>/'+cd_relatorio_auditoria;
    }
    
    function ir_constatacao(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/constatacao"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_acompanhamento(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/acompanhamento"); ?>/'+cd_relatorio_auditoria;
    }
	
	function ir_anexo(cd_relatorio_auditoria)
    {
        location.href='<?php echo site_url("gestao/relatorio_auditoria/anexo"); ?>/'+cd_relatorio_auditoria;
    }
    
    function excluir(cd_relatorio_auditoria_equipe, cd_relatorio_auditoria )
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
        {
            location.href='<?php echo site_url("gestao/relatorio_auditoria/excluir_equipe/"); ?>' + "/" + cd_relatorio_auditoria_equipe+ "/"+cd_relatorio_auditoria
        }
    }
    
    function alterar(cd_relatorio_auditoria_equipe)
    {
        $.post( '<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/carrega_equipe',
        {
            cd_relatorio_auditoria_equipe: cd_relatorio_auditoria_equipe
        },
        function(data)
        {
            if(data)
            {
                $('#cd_relatorio_auditoria_equipe').val(cd_relatorio_auditoria_equipe);
                $("#cd_usuario option[value='"+data.cd_usuario+"']").attr('selected', 'selected');
                $("#tipo option[value='"+data.tipo+"']").attr('selected', 'selected');
            }
        },'json');
    }
    
    function gera_pdf(cd_relatorio_auditoria)
	{
        location.href='<?php echo base_url() . index_page(); ?>/gestao/relatorio_auditoria/gera_pdf/'+cd_relatorio_auditoria;
	}
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_nc', 'Equipe', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Constatação', FALSE, 'ir_constatacao('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Registros Gerais', FALSE, 'ir_acompanhamento('.$cd_relatorio_auditoria.');');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo('.$cd_relatorio_auditoria.');');

$ar_tipo[] = array('value' => 'A', 'text' => 'Auditor');
$ar_tipo[] = array('value' => 'E', 'text' => 'Especialista');
$ar_tipo[] = array('value' => 'O', 'text' => 'Observador');

$body=array();
$head = array(
    'Nome'
    , 'Tipo'
    , ''
);

foreach( $collection as $item )
{
    $body[] = array(
        array($item["nome"],'style="text-align:left;"'),
        $item["tipo"],
        ($fl_permissao ? '<a href="javascript:void(0);" onclick="alterar('.$item["cd_relatorio_auditoria_equipe"].')">[Editar]</a>  <a href="javascript:void(0);" onclick="excluir('.$item["cd_relatorio_auditoria_equipe"].','.$cd_relatorio_auditoria.')">[Excluir]</a>' : '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/relatorio_auditoria/salvar_equipe', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Equipe" );
            echo form_default_hidden('cd_relatorio_auditoria', "Código:", $cd_relatorio_auditoria, "style='width:100%;border: 0px;' readonly" );
            echo form_default_hidden('cd_relatorio_auditoria_equipe', "Código:", '0', "style='width:100%;border: 0px;' readonly" );
            echo form_default_dropdown('cd_usuario', 'Usuário:*', $ar_usuarios);
            echo form_default_dropdown('tipo', 'Tipo:*', $ar_tipo);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start(); 
            if($fl_permissao)
            {
                echo button_save("Salvar");
            }
            echo button_save("Imprimir PDF", "gera_pdf(".$cd_relatorio_auditoria.")", "botao_disabled");
        echo form_command_bar_detail_end();
    echo form_close();

    echo $grid->render();

	echo "<BR><BR><BR>";
echo aba_end();

$this->load->view('footer_interna');
?>