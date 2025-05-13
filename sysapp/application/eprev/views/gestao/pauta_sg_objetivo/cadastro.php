<?php
	set_title('Pauta SG - Objetivo');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('fl_anexo_obrigatorio', 'ds_pauta_sg_objetivo')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pauta_sg_objetivo/index') ?>";
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
    	echo form_open('gestao/pauta_sg_objetivo/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_pauta_sg_objetivo', '', $row['cd_pauta_sg_objetivo']);
                echo form_default_dropdown('fl_anexo_obrigatorio', 'Anexo obrigatório: (*)', $tipo, $row['fl_anexo_obrigatorio']);                
                echo form_default_text('ds_pauta_sg_objetivo', 'Objetivo: (*)', $row['ds_pauta_sg_objetivo'], 'style="width:300px;"');
	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>