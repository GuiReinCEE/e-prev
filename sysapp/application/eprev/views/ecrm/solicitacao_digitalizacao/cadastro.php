<?php
set_title('Solicitação de Digitalização - Cadastro');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'dt_solicitacao_digitalizacao',
		'cd_gerencia_responsavel', 
		'cd_usuario_responsavel',
		'nr_solicitacao_digitalizacao'
	)) ?>

	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url('ecrm/solicitacao_digitalizacao/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var select = $('#cd_usuario_responsavel'); 
						
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
		}, 'json', true);
	}

	function ir_lista()
	{
		location.href = '<?= site_url('ecrm/solicitacao_digitalizacao') ?>';
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_open('ecrm/solicitacao_digitalizacao/salvar');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_solicitacao_digitalizacao', '', $row);
			echo form_default_date('dt_solicitacao_digitalizacao', 'Data: (*)', $row);
			echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência: (*)', $row['cd_gerencia_responsavel'],'onchange="get_usuarios(this.value)"');
			echo form_default_dropdown('cd_usuario_responsavel', 'Usuário: (*)', $responsavel, $row['cd_usuario_responsavel']);
			echo form_default_text('ds_solicitacao_digitalizacao', 'Tipo de Doc. :', $row, 'style="width:350px;"');
			echo form_default_integer('nr_solicitacao_digitalizacao', 'Quant. de Imagem: (*)', $row);	
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar');	
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();

$this->load->view('footer');
?>