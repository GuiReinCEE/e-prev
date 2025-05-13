<?php
	set_title('Registro de Solicitações de Fiscalizações e Auditorias - Origem');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_solic_fiscalizacao_audit_origem', 'fl_especificar')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit_origem/index') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $tipo = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );  
                         
    echo aba_start($abas);
    	echo form_open('atividade/solic_fiscalizacao_audit_origem/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_solic_fiscalizacao_audit_origem', '', $row['cd_solic_fiscalizacao_audit_origem']);
                echo form_default_text('ds_solic_fiscalizacao_audit_origem', 'Origem: (*)', $row['ds_solic_fiscalizacao_audit_origem'], 'style="width:350px;"');
                echo form_default_dropdown('fl_especificar', 'Especificar: (*)', $tipo, $row['fl_especificar']);     
	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>