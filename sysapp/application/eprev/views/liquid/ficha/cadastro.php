<?php
	set_title('LIQUID - Ficha');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ficha', 'ds_ficha')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('liquid/ficha') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('liquid/ficha/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_ficha', '', $row['cd_ficha']);	
				if(intval($row['cd_ficha']) == 0)
				{
					echo form_default_text('nr_ficha', 'Cód: (*)', $row['nr_ficha']);
				}
				else
				{
					echo form_default_row('nr_ficha', 'Cód:', '<span class="badge badge-inverse">'.$row['nr_ficha'].'</span>');
				}
				echo form_default_text('ds_ficha', 'Ficha: (*)', $row['ds_ficha'], 'style="width:500px;"');
				echo form_default_textarea('ds_caminho', 'Caminho: ', $row['ds_caminho'], 'style="width:500px;height:120px;"');
				echo form_default_checkbox_group('ficha_gerencia', 'Gerência:', $gerencia, $ficha_gerencia , 120);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();

	$this->load->view('footer');
?>