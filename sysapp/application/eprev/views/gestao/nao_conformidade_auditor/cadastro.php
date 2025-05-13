<?php
set_title('Não Conformidades Auditor');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(Array('cd_usuario_titular', 'cd_usuario_substituto'));
?>
    
    function ir_lista()
    {
            location.href='<?php echo site_url("gestao/nao_conformidade_auditor"); ?>';
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('gestao/nao_conformidade_auditor/salvar', 'name="filter_bar_form"');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_hidden('cd_processo', '', $cd_processo);
            echo form_default_hidden('fl_status', '', $fl_status);
			echo form_default_row('ds_processo', 'Processo:', $ds_processo);
            echo form_default_dropdown('cd_usuario_titular', '1º opção *:', $arr_usuario_titular, array($cd_usuario_titular));
            echo form_default_dropdown('cd_usuario_substituto', '2º opção *:', $arr_usuario_substituto, array($cd_usuario_substituto));
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();         
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
    
    echo form_close();
	echo "<BR><BR><BR>";	

echo aba_end();

$this->load->view('footer_interna');
?>