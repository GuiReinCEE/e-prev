<?php
	set_title('Pendências');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_pendencia_minha', 'ds_pendencia_minha')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('servico/pendencia_minha/index') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
		
	echo aba_start($abas);
		echo form_open('servico/pendencia_minha/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_pendencia_minha_old', '', $row['cd_pendencia_minha']);

				if(trim($row['cd_pendencia_minha']) == '')
				{		
					echo form_default_text('cd_pendencia_minha', 'Código: (*)');
				}
				else
				{
					echo form_default_row('cd_pendencia_minha_hidden', 'Código:', $row['cd_pendencia_minha']);
				}
				echo form_default_text('ds_pendencia_minha', 'Descrição: (*)', $row['ds_pendencia_minha'], 'style="width:300px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();     
	            echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>