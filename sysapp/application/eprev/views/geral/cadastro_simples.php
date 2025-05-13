<?php
$data['topo_titulo'] = 'Cadastro Simples';
$this->load->view(get_header_sem_topo(), $data);
?>

<script type="text/javascript">
<?php
	echo form_default_js_submit(array('cadastro_simples_descricao'));
?>
</script>
<style>
	body {
		padding: 0px;
		margin-top: 0px;
		margin-bottom: 0px;
		margin-left: 5px;
		margin-right: 5px;
	}
</style>
<?php 
echo form_open('geral/cadastro_simples_salvar');
	echo form_start_box("default_box", "Cadastro");
		echo form_default_hidden("table", "", $table);
		echo form_default_hidden("field_pk", "", $field_pk);
		echo form_default_hidden("field_text", "", $field_text);
		echo form_default_hidden("callback", "", $callback);
		echo form_default_hidden("fechar", "", $fechar);
		echo form_default_text("cadastro_simples_descricao", "Descrição:(*)", "", 'style="width: 180px;"');
	echo form_end_box("default_box");

echo form_command_bar_detail_start();
	echo button_save("Incluir");
	echo button_save("Fechar","return parent.$fechar();","botao_disabled");
echo form_command_bar_detail_end();
echo form_close();
$this->load->view('footer.php');
?>
