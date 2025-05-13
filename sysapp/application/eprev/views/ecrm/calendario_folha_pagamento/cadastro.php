<?php
set_title('Calendário de folha de pagamento - Cadastro');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'dt_calendario_folha_pagamento',
		'ds_calendario_folha_pagamento'
	)) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('ecrm/calendario_folha_pagamento') ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('ecrm/calendario_folha_pagamento/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_calendario_folha_pagamento', '', $row);
			echo form_default_date('dt_calendario_folha_pagamento', 'Data: (*)', $row);
			echo form_default_text('ds_calendario_folha_pagamento', 'Descrição : (*)', $row, 'style="width:350px;"');
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar');	
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();

$this->load->view('footer');
?>