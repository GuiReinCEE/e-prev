<?php 
	set_title('Autoatendimento Usuário Acesso - Cadastro');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('arquivo', 'fl_tipo', 'dt_referencia')) ?>

	function ir_lista()
	{
		location.href="<?= site_url('servico/autoatendimento_contracheque') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
	echo form_open('servico/autoatendimento_contracheque/salvar');
	echo form_start_box('default_box', 'Cadastro');
		echo form_default_hidden('cd_contracheque_imagem', '', $row['cd_contracheque_imagem']);
		echo form_default_date('dt_referencia', 'Data Referência :*', $row['dt_referencia']);
		echo form_default_dropdown('fl_tipo', 'Tipo :*', $tipo, $row['fl_tipo']);
		echo form_default_upload_iframe('arquivo', 'contracheque_imagem', 'Arquivo:', array($row['arquivo'], $row['arquivo_nome']));
	echo form_end_box('default_box');

	echo form_command_bar_detail_start();
		echo button_save();
	echo form_command_bar_detail_end();

	echo form_close();
	echo aba_end();
	$this->load->view('footer_interna');
?>