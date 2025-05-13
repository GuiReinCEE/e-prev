<?php 
	set_title('Autoatendimento Usuário Acesso - Cadastro');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('cd_usuario', 'fl_usuario')) ?>

	function ir_lista()
	{
		location.href="<?= site_url('servico/autoatendimento_usuario_acesso') ?>";
	}
	
	function get_usuarios()
	{
		var cd_gerencia = $("#cd_gerencia").val();
		
		$.post("<?= site_url('servico/autoatendimento_usuario_acesso/get_usuarios/') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{ 
			var select = $('#cd_usuario'); 
			
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
		}, 'json');
	}	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
	echo form_open('servico/autoatendimento_usuario_acesso/salvar');
	echo form_start_box('default_box', 'Usuário');
		echo form_default_gerencia('cd_gerencia', 'Gerência: (*)', '', 'onchange="get_usuarios()"');
		echo form_default_dropdown('cd_usuario', 'Usuário: (*)'); 
	echo form_end_box('default_box');

	echo form_command_bar_detail_start();
		echo button_save();
	echo form_command_bar_detail_end();

	echo aba_end();
	echo form_close();

	$this->load->view('footer_interna');
?>