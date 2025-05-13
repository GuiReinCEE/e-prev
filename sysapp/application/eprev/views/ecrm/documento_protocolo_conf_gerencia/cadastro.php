<?php 
	set_title('Conferência de Documentos - Cadastro');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('cd_usuario_responsavel', 'nr_amostragem')) ?> 

	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/documento_protocolo_conf_gerencia'); ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
        echo form_open('ecrm/documento_protocolo_conf_gerencia/salvar');
	        echo form_start_box('cadastro_box', 'Cadastro');
	            echo form_default_hidden('cd_documento_protocolo_conf_gerencia', '', $row);
	            echo form_default_row('', 'Gerência:', $row['cd_gerencia']);
	            echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuarios, $row['cd_usuario_responsavel']);
	            echo form_default_numeric('nr_amostragem', '% de Amostragem:', number_format($row['nr_amostragem'], 2, ',', '.'));
	        echo form_end_box('cadastro_box');
	        echo form_command_bar_detail_start();
	        	echo button_save();
	        echo form_command_bar_detail_end();
        echo form_close();
	echo aba_end();
	echo br();
	$this->load->view('footer');
?>