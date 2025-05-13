<?php
set_title('Formulários - Lista');
$this->load->view('header');
?>
<script>
    <?php
    echo form_default_js_submit(Array('cd_plano_empresa', 'cd_plano', 'arquivo', 'nome_arquivo'));
    ?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/formulario"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);
    echo form_open('ecrm/formulario/salvar', 'name="filter_bar_form"');
        echo form_start_box("default_box", "Cadastro");
            echo filter_plano_ajax('cd_plano', '', '', 'Empresa:(*)', 'Plano:(*)','I');
            echo form_default_upload_iframe('arquivo','extranet_formulario','Arquivo:', '', '');
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    echo form_close();
    echo "<BR><BR><BR>";

echo aba_end();

$this->load->view('footer');
?>