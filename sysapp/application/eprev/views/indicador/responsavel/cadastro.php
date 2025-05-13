<?php 
	set_title('Responsáveis pelos Indicadores');
	$this->load->view('header'); 
?>
<script>
	<?= form_default_js_submit(array('cd_gerencia', 'cd_usuario')) ?>

	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url('indicador/responsavel/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var usuario = $("#cd_usuario"); 
									
			if(usuario.prop) 
			{
				var usuario_opt = usuario.prop("options");
			}
			else
			{
				var usuario_opt = usuario.attr("options");
			}

			$("option", usuario).remove();

			usuario_opt[usuario_opt.length] = new Option("Selecione", "");

			$.each(data, function(val, text) {
				usuario_opt[usuario_opt.length] = new Option(text.text, text.value);
			});

		}, "json", true);
	}

	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
		   location.href = "<?= site_url('indicador/responsavel/excluir/'.intval($row['cd_indicador_administrador'])) ?>";
		}
	}	
	
	function ir_lista()
	{
		location.href = "<?= site_url('indicador/responsavel') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista',   'Lista',    FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', TRUE,  'location.reload();');
	
	echo aba_start($abas);
		echo form_open('indicador/responsavel/salvar');
			echo form_start_box('default_box', 'Responsáveis pelos Indicadores');
				echo form_hidden('cd_indicador_administrador', intval($row['cd_indicador_administrador']));

				if(intval($row['cd_indicador_administrador']) == 0)
				{
					echo form_default_dropdown('cd_gerencia', 'Gerência: (*)', $gerencia, $row['cd_gerencia'], 'onchange="get_usuarios(this.value)"');
					echo form_default_dropdown('cd_usuario', 'Usuário: (*)', $usuario, $row['cd_usuario']);
				}
				else
				{
					echo form_default_row('cd_gerencia', 'Gerência:', $row['gerencia']);
					echo form_default_row('ds_usuario', 'Usuário:', $row['ds_usuario']);
				}
				echo form_default_checkbox_group('grupo', 'Grupos:', $grupo, $administrador_indicador_grupo, 300);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save();

				if(intval($row['cd_indicador_administrador']) > 0)
				{
					echo button_save('Excluir', 'excluir()', 'botao_vermelho');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>