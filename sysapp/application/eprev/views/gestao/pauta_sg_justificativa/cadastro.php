<?php
	set_title('Pauta SG - Justificativa');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_pauta_sg_justificativa')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pauta_sg_justificativa/index') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
    	echo form_open('gestao/pauta_sg_justificativa/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_pauta_sg_justificativa', '', $row['cd_pauta_sg_justificativa']);
                echo form_default_text('ds_pauta_sg_justificativa', 'Justificativa: (*)', $row['ds_pauta_sg_justificativa'], 'style="width:300px;"');
	    	echo form_end_box('default_box');
	    	echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>