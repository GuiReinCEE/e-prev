<?php
	set_title('Protocolo Secretária');
	$this->load->view("header");
?>
<script>

	<?= form_default_js_submit(array('ds_protocolo_sg', 'cd_gerencia_responsavel', 'cd_usuario_responsavel')) ?>

	function get_usuarios(cd_gerencia, $t)
	{
		$.post("<?= site_url('cadastro/protocolo_sg/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var usuario = $t; 
									
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

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/protocolo_sg') ?>";
	}

	function enviar()
	{
		var confirmacao = 'Deseja enviar?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/protocolo_sg/enviar/'.intval($row['cd_protocolo_sg'])) ?>";
		}
	}

	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/protocolo_sg/excluir/'.intval($row['cd_protocolo_sg'])) ?>";
		}
	}

	$(function(){
		$("#fl_conhecimento").change(function(){
			if($(this).val() == 'N')
			{
				$("#dt_prazo_row").show();
			}
			else
			{
				$("#dt_prazo_row").hide();
			}
		});

		$("#fl_conhecimento").change();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('cadastro/protocolo_sg/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_hidden('cd_protocolo_sg', $row['cd_protocolo_sg']);

				if(intval($row['cd_protocolo_sg']) > 0)
				{
					echo form_default_row('ano_numero', 'Ano/Número:', '<span class="label label-inverse">'.$row['ano_numero'].'</span>');
				}
				
				if(trim($row['dt_envio']) != '')
				{
					echo form_default_row('dt_envio', 'Dt. Envio:', '<span class="label label-success">'.$row['dt_envio'].'</span>');
				}

				echo form_default_text('ds_protocolo_sg', 'Documento: (*)', $row, 'style="width:450px;"');
				echo form_default_dropdown('cd_gerencia_responsavel', 'Gerência Responsável: (*)', $gerencia, $row['cd_gerencia_responsavel'], 'onchange="get_usuarios(this.value, $(\'#cd_usuario_responsavel\'))"');
				echo form_default_dropdown('cd_usuario_responsavel', 'Usuário Responsável: (*)', $usuario_responsavel, $row['cd_usuario_responsavel']);

				echo form_default_dropdown('cd_gerencia_substituto', 'Gerência Substituto:', $gerencia, $row['cd_gerencia_substituto'], 'onchange="get_usuarios(this.value, $(\'#cd_usuario_substituto\'))"');
				echo form_default_dropdown('cd_usuario_substituto', 'Usuário Substituto:', $usuario_substituto, $row['cd_usuario_substituto']);

				echo form_default_dropdown('fl_conhecimento', 'Para Conhecimento: (*)', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), $row['fl_conhecimento']);
				echo form_default_date('dt_prazo', 'Dt. Prazo: (*)', $row);
				echo form_default_upload_iframe('arquivo', 'protocolo_sg', 'Arquivo:', array($row['arquivo'], $row['arquivo_nome']), 'protocolo_sg', true);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();   
				if(trim($row['dt_envio']) == '')
				{
					echo button_save('Salvar');

					if(intval($row['cd_protocolo_sg']) > 0)
					{
						echo button_save('Enviar', 'enviar()', 'botao_verde');
						echo button_save('Excluir', 'excluir()', 'botao_vermelho');
					}
				}
	        echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();

	$this->load->view('footer_interna');
?>