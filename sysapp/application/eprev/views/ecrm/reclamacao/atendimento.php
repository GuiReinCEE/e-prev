<?php
	set_title('Reclamações e Sugestões - Atendimento');
	$this->load->view('header');
?>
<script>
	<?php
		$validar = array();

		if(gerencia_in(array('GP')))
		{
			$validar = array('cd_reclamacao_programa', 'dt_prazo', 'data');
		}

		if(intval($row['dt_inclusao']) == '')
		{
			$validar = array_merge($validar, array('cd_divisao', 'cd_usuario_responsavel'));
		}
	
		echo form_default_js_submit($validar);
	?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/anexo/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_reencaminhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/reencaminhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/acompanhamento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_retorno()
	{
		location.href = "<?= site_url('ecrm/reclamacao/retorno/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}	

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}
	
	function get_usuarios()
	{
		var cd_divisao = $("#cd_divisao").val();
		
		$.post("<?= site_url('ecrm/reclamacao/get_usuarios/') ?>",
		{
			cd_divisao : cd_divisao
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
		}, 'json');
	}
	
	function get_ferias()
	{
		var cd_usuario = $("#cd_usuario_responsavel").val();

		ajax_ferias(cd_usuario);
	}

	function ajax_ferias(cd_usuario)
	{
		$.post("<?= site_url('ecrm/reclamacao/get_ferias') ?>",
		{
			cd_usuario : cd_usuario
		},
		function(data)
		{ 
			if(data.dt_ferias_ini !== undefined && data.dt_ferias_fim !== undefined)
			{
				alert("Atenção! \n\nUsuário de férias de "+data.dt_ferias_ini+" até "+data.dt_ferias_fim);
			}
		}, 'json');
	}
	
	$(function(){
		configure_result_table();

		default_conceito_box_box_recolher();
	});
</script>
<style>
    #conceito_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_reclamacao', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_atendimento', 'Atendimento', TRUE, 'location.reload();');

	if($permissao['fl_aba_prorrogacao'])
	{	
		$abas[] = array('aba_reencaminahemnto', 'Reencaminhamento', FALSE, 'ir_reencaminhamento();');
		$abas[] = array('aba_prorrogacao', 'Prorrogação', FALSE, 'ir_prorrogacao();');
	}

	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	if($permissao['fl_aba_acao'])
	{
		$abas[] = array('aba_acao', 'Ação', FALSE, 'ir_acao();');
	}

	if($permissao['fl_aba_retorno'])
	{
		$abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');
	}

	if($permissao['fl_aba_comite'])
	{
		$abas[] = array('aba_validacao_comite', 'Validação Comitê', FALSE, 'ir_validacao();');
	}

	if($permissao['fl_aba_parecer_final'])
	{
		$abas[] = array('aba_parecer_final', 'Parecer Final', FALSE, 'ir_parecer_final();');
	}

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Atendimento:', 'Indicar o responsável pelo tratamento da reclamação.');
		echo form_end_box('default_conceito_box');

		echo form_open('ecrm/reclamacao/salvar_atendimento');
			echo form_start_box('default_box', 'Encaminhamento');
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('tipo', '', $row['tipo']);
				echo form_default_hidden('cd_operacao', '', $row['cd_operacao']);

				echo form_default_row('numero', 'Número:', $row['numero']);
				echo form_default_row('ano', 'Ano:', $row['ano']);
				echo form_default_row('ds_tipo', 'Tipo:', $row['ds_tipo']);	
				
				if(trim($row['dt_atualizacao']) != '')
				{
					echo form_default_row('dt_inclusao', 'Dt. Encaminhado:', $row['dt_inclusao']);
					echo form_default_row('ds_usuario_inclusao', 'Usuário:', $row['ds_usuario_inclusao']);
				}

				if(trim($row['dt_atualizacao']) != '')
				{
					echo form_default_row('dt_atualizacao', 'Dt. Atualização:', $row['dt_atualizacao']);
					echo form_default_row('ds_usuario_atualizacao', 'Usuário:', $row['ds_usuario_atualizacao']);		
				}

				if(gerencia_in(array('GCM')))
				{
					echo form_default_dropdown('cd_reclamacao_programa', 'Programa: (*)', $programa, $row['cd_reclamacao_programa'], 'style="width:500px;"');
					echo form_default_date('dt_prazo', 'Dt. Prazo (5 dias úteis): (*)', $row);
				}
				else
				{
					echo form_default_hidden('cd_reclamacao_programa', '', $row['cd_reclamacao_programa']);
					echo form_default_hidden('dt_prazo', '', $row['dt_prazo']);
					echo form_default_row('dt_prazo', 'Dt. Prazo:', $row['dt_prazo']);
					if(trim($row['dt_prorrogacao']) != '')
					{
						echo form_default_row('dt_prorrogacao', 'Dt. Prorrogação:', $row['dt_prorrogacao']);
					}
				}

				if(intval($row['dt_inclusao']) == '')
				{
					echo form_default_gerencia('cd_divisao', 'Gerência: (*)', $row['cd_divisao'], 'onchange="get_usuarios()"');
					echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuarios, $row['cd_usuario_responsavel'], 'onchange="get_ferias()"'); 
				}
				else
				{
					echo form_default_row('cd_divisao', 'Gerência: ', $row['ds_divisao']);
					echo form_default_row('', 'Responsável: ', $row['ds_usuario_reponsavel']);

					echo form_default_hidden('cd_divisao', ' ', $row['cd_divisao']);
					echo form_default_hidden('cd_usuario_responsavel', '', $row['cd_usuario_responsavel']);
				}
			echo form_end_box('default_box');
			
			echo form_command_bar_detail_start();
				if($permissao['fl_acao'] AND (trim($row['dt_inclusao']) == '' OR (gerencia_in(array('GP')))))
				{		
					echo button_save('Salvar');
				}
			echo form_command_bar_detail_end();
		echo form_close();
	echo aba_end();
	$this->load->view('footer_interna');
?>