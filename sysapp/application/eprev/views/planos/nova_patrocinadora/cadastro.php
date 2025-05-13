<?php
	set_title('Operacionalização de Nova Patrocinadora');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'nr_nova_patrocinadora_estrutura', 
		'ds_nova_patrocinadora_estrutura', 
		'cd_gerencia', 
		'cd_usuario_responsavel', 
		'cd_usuario_substituto', 
		'nr_prazo'
	)) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('planos/nova_patrocinadora') ?>";
	}
    
	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url('planos/nova_patrocinadora/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var responsavel = $("#cd_usuario_responsavel"); 
									
			if(responsavel.prop) 
			{
				var responsavel_opt = responsavel.prop("options");
			}
			else
			{
				var responsavel_opt = responsavel.attr("options");
			}

			$("option", responsavel).remove();

			responsavel_opt[responsavel_opt.length] = new Option("Selecione", "");

			var substituto = $("#cd_usuario_substituto"); 
									
			if(substituto.prop) 
			{
				var substituto_opt = substituto.prop("options");
			}
			else
			{
				var substituto_opt = substituto.attr("options");
			}

			$("option", substituto).remove();

			substituto_opt[substituto_opt.length] = new Option("Selecione", "");

			$.each(data, function(val, text) {
				responsavel_opt[responsavel_opt.length] = new Option(text.text, text.value);
				substituto_opt[substituto_opt.length] = new Option(text.text, text.value);
			});

		}, "json", true);
	}

	function desativar()
	{
		var confirmacao = 'Deseja Desativar a Atividade?\n\n'+
	        'Clique [Ok] para Sim\n\n'+
	        'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('planos/nova_patrocinadora/desativar/'.$row['cd_nova_patrocinadora_estrutura']) ?>';
        }
	}

	function ativar()
	{
		var confirmacao = 'Deseja Ativar a Atividade?\n\n'+
	        'Clique [Ok] para Sim\n\n'+
	        'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = '<?= site_url('planos/nova_patrocinadora/ativar/'.$row['cd_nova_patrocinadora_estrutura']) ?>';
        }
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('planos/nova_patrocinadora/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_nova_patrocinadora_estrutura', '', $row['cd_nova_patrocinadora_estrutura']);	
				echo form_default_integer('nr_nova_patrocinadora_estrutura', 'Nº Atividade: (*)', $row['nr_nova_patrocinadora_estrutura']);
				echo form_default_text('ds_nova_patrocinadora_estrutura', 'Atividade: (*)', $row, 'style="width:350px;"');
				echo form_default_textarea('ds_atividade', 'Descrição Detalhada:', $row, 'style="height:100px;"');
				echo form_default_gerencia('cd_gerencia', 'Gerência: (*)', $row['cd_gerencia'], 'onchange="get_usuarios(this.value)"');
				echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $responsavel, $row['cd_usuario_responsavel']);
				echo form_default_dropdown('cd_usuario_substituto', 'Substituto: (*)', $substituto, $row['cd_usuario_substituto']);
				echo form_default_integer('nr_prazo', 'Prazo (dias): (*)', $row['nr_prazo']);
				echo form_default_checkbox_group('atividade_estrutura_dependencia','Atividades Dependentes:', $atividade, $atividade_estrutura_dependencia, 200, 450);
				echo form_default_textarea('ds_observacao', 'Observação:', $row, 'style="height:100px;"');

				if(trim($row['dt_desativado']) != '')
				{
					echo form_default_row('', 'Dt. Desativado', $row['dt_desativado']);
					echo form_default_row('', 'Usuário', $row['ds_usuario_desativado']);
				}

			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				if(trim($row['dt_desativado']) == '')
				{
					echo button_save('Salvar');	
				}
				
				if(intval($row['cd_nova_patrocinadora_estrutura']) > 0)
				{
					if(trim($row['dt_desativado']) == '')
					{
						echo button_save('Desativar', 'desativar();', 'botao_vermelho');	
					}
					else
					{
						echo button_save('Ativar', 'ativar();', 'botao_verde');	
					}
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>