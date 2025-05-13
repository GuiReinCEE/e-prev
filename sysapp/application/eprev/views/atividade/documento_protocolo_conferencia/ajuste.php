<?php 
	set_title('Conferência de Documentos');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('ds_ajuste')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/documento_protocolo_conferencia') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_ajustes', 'Ajustes', TRUE, 'location.reload();');

    echo aba_start($abas);
		echo form_start_box('default_documento_box', 'Documento');
			echo form_default_row('', 'Protocolo:', $row['nr_protocolo']);
			echo form_default_row('', 'Envio:', $row['dt_envio']);
			echo form_default_row('', 'Usuário Envio:', $row['ds_usuario_envio']);
			echo form_default_row('', 'Recebimento:', $row['dt_recebimento']);
			echo form_default_row('', 'Usuário Receb.:', $row['ds_usuario_recebimento']);
			echo form_default_row('', 'Indexação:', $row['dt_indexacao']);
			echo form_default_row('', 'RE:', $row['nr_re']);
			echo form_default_row('', 'Participante:', $row['ds_participante']);
			echo form_default_row('', 'Doc.:', $row['ds_documento']);
			echo form_default_row('', 'Tipo de Documento:', $row['cd_tipo_doc']);
			echo form_default_row('', 'Caminho:', $row['ds_caminho']);
			echo form_default_row('', 'Folhas:', $row['nr_folha']);
			echo form_default_row('', 'Processo:', $row['ds_processo']);
			echo form_default_row('', 'Arquivo:', $row['arquivo']);
		echo form_end_box('default_documento_box');
		echo form_open('atividade/documento_protocolo_conferencia/salvar_ajuste');
    		echo form_start_box('default_cadastro_box', 'Cadastro');
    			echo form_default_hidden('cd_documento_protocolo_conf_gerencia_item', '', $row['cd_documento_protocolo_conf_gerencia_item']);
    			echo form_default_textarea('ds_ajuste', 'Ajuste: (*)', $row['ds_ajuste']);
    		echo form_end_box('default_cadastro_box');
    		echo form_command_bar_detail_start();
				echo button_save('Salvar');  					
	    	echo form_command_bar_detail_end();
		echo form_close();
    echo aba_end();
    $this->load->view('footer');
?>