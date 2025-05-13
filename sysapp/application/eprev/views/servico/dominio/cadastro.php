<?php
set_title('Controles TI - Cadastro');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('descricao', 'cd_dominio_tipo','dt_dominio_renovacao')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/dominio') ?>';
	}

	function ir_renovacao()
	{
		location.href = '<?= site_url('servico/dominio/renovacao/'.$row['cd_dominio']) ?>' ;
	}

	function ir_anexo()
	{
		location.href = '<?= site_url('servico/dominio/anexo/'.$row['cd_dominio']) ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_dominio']) > 0)
{
	$abas[] = array('aba_renovacao', 'Renovação', FALSE, 'ir_renovacao();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
}

echo aba_start($abas);
	echo form_open('servico/dominio/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_dominio', '', $row);	
			echo form_default_dropdown('cd_dominio_tipo', 'Tipo Controle: (*)', $tipo_dominio, $row['cd_dominio_tipo']);
			echo form_default_text('descricao', 'Descrição: (*)', $row, 'style="width:350px;"');
			echo form_default_date('dt_dominio_renovacao', 'Dt. Expiração: (*)', $row);
			echo form_default_text('ds_dominio', 'Link: ', $row, 'style="width:350px;"');
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
				echo button_save('Salvar');	
		echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer');
?>