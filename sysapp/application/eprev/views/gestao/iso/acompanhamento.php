<?php
set_title('Pendências das Auditorias ISO');
$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array());
?>

function irLista()
{
    location.href='<?php echo site_url("gestao/iso"); ?>';
}

function irCadastro(cd)
{
    location.href='<?php echo site_url("gestao/iso/cadastro"); ?>/'+cd;
}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'irCadastro('.$cd_pendencia_auditoria_iso.');');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('gestao/iso/salva_acompanhamento', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_pendencia_auditoria_iso', "Código:", $cd_pendencia_auditoria_iso, "style='width:100%;border: 0px;' readonly" );
            echo form_default_textarea('ds_pendencia_auditoria_iso_acompanhamento', "Observação:*", '', "style='width:500px; height:90px;'");
        echo form_end_box("default_box");

        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();

    echo form_close();

    $body=array();
    $head = array(
        'Dt. Inclusão',
        'Acompanhamento',
        'Usuário'
    );

    foreach($collection as $item )
    {
        $body[] = array(
        $item["dt_inclusao"],
        array($item["ds_pendencia_auditoria_iso_acompanhamento"],"text-align:justify;"),
        array($item["nome"],"text-align:left;")
        );
    }

    $this->load->helper('grid');
        $grid = new grid();
        $grid->head = $head;
        $grid->body = $body;
        echo $grid->render();

    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer_interna');
?>