<?php
set_title('Controle IGP');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ano')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/controle_igp') ?>";
	}
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('gestao/controle_igp/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_controle_igp', '', $row);	
			echo form_default_integer('nr_ano', 'Ano: (*)', $row);
		echo form_end_box('default_box');

		echo form_command_bar_detail_start();
			if(intval($row['cd_controle_igp']) == 0)
			{
				echo button_save('Salvar');	
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>