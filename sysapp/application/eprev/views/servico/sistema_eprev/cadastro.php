<?php
	set_title('Sistema e-prev - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array("ds_sistema", "cd_gerencia_responsavel", "cd_usuario_responsavel", "cd_usuario_solicitante", "ds_controller")) ?>

	function ir_lista()
	{
		location.href = "<?= site_url("servico/sistema_eprev/index") ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url("servico/sistema_eprev/acompanhamento/".intval($row["cd_sistema"]))?>";
	}

	function ir_atividade()
	{
		location.href = "<?= site_url("servico/sistema_eprev/atividade/".intval($row["cd_sistema"])) ?>";
	}
	
	function ir_anexo()
	{
		location.href = "<?= site_url("servico/sistema_eprev/anexo/".$row["cd_sistema"]) ?>";
	}

	function ir_rotina()
	{
		location.href = "<?= site_url("servico/sistema_eprev/rotina/".intval($row["cd_sistema"])) ?>";
	}

	function ir_metodo()
	{
		location.href = "<?= site_url("servico/sistema_eprev/metodo/".intval($row["cd_sistema"])) ?>";
	}

	function gerar_pdf()
	{
		window.open("<?=site_url("servico/sistema_eprev/pdf/".intval($row["cd_sistema"])) ?>");
	}

	function ir_pendencia()
	{
		location.href ="<?= site_url("servico/sistema_eprev/pendencia/".intval($row['cd_sistema'])) ?>";
	}

	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url("servico/sistema_eprev/get_usuarios") ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var solciitante = $("#cd_usuario_solicitante"); 
			var responsavel = $("#cd_usuario_responsavel");
									
			if(solciitante.prop) 
			{
				var solciitante_opt = solciitante.prop("options");
			}
			else
			{
				var solciitante_opt = solciitante.attr("options");
			}

			$("option", solciitante).remove();

			solciitante_opt[solciitante_opt.length] = new Option("Selecione", "");
	
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

			$.each(data, function(val, text) {
				solciitante_opt[solciitante_opt.length] = new Option(text.text, text.value);

				responsavel_opt[responsavel_opt.length] = new Option(text.text, text.value);

			});

		}, "json", true);
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_sistema']) > 0)
	{
		$abas[] = array('aba_metodo', 'Método',FALSE, 'ir_metodo();');
		$abas[] = array('aba_rotina', 'Rotina', FALSE, 'ir_rotina();');
		$abas[] = array('aba_pendencia', 'Pendências', FALSE, 'ir_pendencia();');
		$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
		$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');		
		$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
 		
	}
			 
	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/salvar');
			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_sistema', '', $row['cd_sistema']);
				echo form_default_text('ds_sistema', 'Sistema: (*)', $row['ds_sistema'], 'style="width:300px;"');
				echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência Responsável: (*)', $row['cd_gerencia_responsavel'], 'onchange="get_usuarios(this.value)"');
				echo form_default_dropdown('cd_usuario_solicitante', 'Solicitante: (*)', $solicitante, $row['cd_usuario_solicitante']);
				echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $responsavel, $row['cd_usuario_responsavel']);
				echo form_default_text('ds_controller', 'Controller: (*)', $row['ds_controller'], 'style="width:300px;"');
				echo form_default_date('dt_publicacao', 'Dt. Publicação: ',	$row['dt_publicacao']);
				echo form_default_textarea('ds_descricao', 'Descrição: ', $row['ds_descricao']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();     
	            echo button_save('Salvar');
	            
	            if(intval($row['cd_sistema']) > 0)
				{
	            	echo button_save('PDF', 'gerar_pdf()', 'botao_verde');
	            }
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>