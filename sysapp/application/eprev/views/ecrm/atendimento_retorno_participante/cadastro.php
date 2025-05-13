<?php
	set_title('Retorno ao Participante - Cadastro');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('dt_retorno')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/atendimento_retorno_participante') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
    	echo form_open('ecrm/atendimento_retorno_participante/salvar');
	    	echo form_start_box('default_cadastro_box', 'Cadastro');
	    		echo form_default_hidden('cd_atendimento_retorno_participante', '', $row);
	    		echo form_default_row('', 'Nº Atividade:', $row['cd_atividade']);
	    		echo form_default_row('', 'RE:', $row['ds_re']);
	    		echo form_default_row('', 'Nome´Participante:', $row['nome']);
	    		echo form_default_date('dt_retorno', 'Dt. Retorno: (*)', $row);
	    		echo form_default_textarea('ds_observacao', 'Observações:', $row, 'style="width:400px; height:80px;"');
	    	echo form_end_box('default_cadastro_box');
	    	echo form_command_bar_detail_start();
	            echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>