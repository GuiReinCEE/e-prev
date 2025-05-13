<?php
	set_title('Tipos de Controles TI - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_dominio_tipo', 'cd_usuario_responsavel', 'cd_usuario_substituto')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/dominio_tipo') ?>';
	}

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('servico/dominio_tipo/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_dominio_tipo', '', $row['cd_dominio_tipo']);
				echo form_default_text('ds_dominio_tipo', 'Descrição: (*)', $row['ds_dominio_tipo'], 'style="width:350px;"');
				echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuario , $row['cd_usuario_responsavel']);
				echo form_default_dropdown('cd_usuario_substituto', 'Substituto: (*)', $usuario, $row['cd_usuario_substituto']);
				echo form_default_integer('nr_dias', 'Qt dia(s): ', $row['nr_dias']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();
	$this->load->view('footer');
?>