<?php
set_title('Pauta CCI');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_pauta_cci', 'dt_pauta_cci', 'hr_pauta_cci')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('gestao/pauta_cci') ?>';
	}

	function ir_assunto()
	{
		location.href = '<?= site_url('gestao/pauta_cci/assunto').'/'.$row['cd_pauta_cci'] ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_pauta_cci']) > 0)
{
	$abas[] = array('aba_assunto', 'Assunto', FALSE, 'ir_assunto();');
}

echo aba_start($abas);
	echo form_open('gestao/pauta_cci/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_pauta_cci', '', $row);	

			echo form_default_integer('nr_pauta_cci', 'Número da Ata : (*)', $row);
			echo form_default_text('ds_local', 'Local :', $row, 'style="width:350px;"');
			echo form_default_date('dt_pauta_cci', 'Dt. Reunião : (*)', $row);
			echo form_default_time('hr_pauta_cci', 'Hr. Reunião : (*)', $row);

			echo form_default_date('dt_pauta_cci_fim', 'Dt. Reunião Encerramento :', $row);
			echo form_default_time('hr_pauta_cci_fim', 'Hr. Reunião Encerramento :', $row);

		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			if(trim($row['dt_aprovacao']) == '')
			{
				echo button_save('Salvar');	
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer');
?>