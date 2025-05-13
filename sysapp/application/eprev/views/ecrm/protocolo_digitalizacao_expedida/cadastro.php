<?php
	set_title('Protocolo Digitalização Expedida');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array('cd_atendimento_protocolo_tipo', 'cd_atendimento_protocolo_discriminacao')) ?>
</script>

<script>
	function ir_lista()
	{
		location.href =" <?= site_url('ecrm/atendimento_protocolo') ?>";
	}

	function ir_protocolo_digitalizacao()
	{
		location.href = "<?= site_url('ecrm/protocolo_digitalizacao_expedida') ?>";
	}
</script>

<?php
	$abas[] = array('aba_lista', 'Correspondência Expedida', FALSE, 'ir_lista();');
	$abas[] = array('aba_protocolo_digitalizacao', 'Protocolo Digitalização', FALSE, 'ir_protocolo_digitalizacao();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);
    echo form_open('ecrm/protocolo_digitalizacao_expedida/gerar_protocolo_expedido');
	    echo form_start_box('default_box', 'Nova');
			echo form_default_hidden('cd_protocolo_digitalizacao_expedida', '', $cd_protocolo_digitalizacao_expedida);
	        echo form_default_dropdown('cd_atendimento_protocolo_tipo', 'Tipo: (*)', $tipo);
	        echo form_default_dropdown('cd_atendimento_protocolo_discriminacao', 'Discriminação: (*)', $discriminacao);
			echo form_default_text('ds_identificacao','', '', "style='width:500px;'");
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();   
			echo button_save('Salvar');
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>