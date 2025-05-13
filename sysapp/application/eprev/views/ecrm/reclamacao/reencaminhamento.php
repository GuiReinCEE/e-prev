<?php
	set_title('Reclamações e Sugestões - Reencaminhamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_divisao_reencaminhamento', 'cd_usuario_responsavel_reencaminhamento')); ?>
	
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

	function ir_atendimento()
	{
		location.href = "<?= site_url('ecrm/reclamacao/atendimento/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/prorrogacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function ir_validacao()
	{
		location.href = "<?= site_url('ecrm/reclamacao/validacao_comite/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}	

	function ir_parecer_final()
	{
		location.href = "<?= site_url('ecrm/reclamacao/parecer_comite_avaliacao/'.$row['numero'].'/'.$row['ano'].'/'.$row['tipo']) ?>";
	}

	function get_usuarios_reencaminhamento()
	{
		var cd_divisao_reencaminhamento = $("#cd_divisao_reencaminhamento").val();
		
		$.post("<?= site_url('ecrm/reclamacao/get_usuarios/') ?>",
		{
			cd_divisao : cd_divisao_reencaminhamento
		},
		function(data)
		{ 
			var select = $('#cd_usuario_responsavel_reencaminhamento'); 
			
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

	function get_ferias_reencaminhamento()
	{
		var cd_usuario = $("#cd_usuario_responsavel_reencaminhamento").val();
		
		ajax_ferias(cd_usuario);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
		    "DateTimeBR"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(3, true);
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
	//$abas[] = array('aba_atendimento', 'Atendimento', FALSE, 'ir_atendimento();');
	$abas[] = array('aba_prorrogacao', 'Reencaminhamento', TRUE, 'location.reload();');
	$abas[] = array('aba_prorrogacao', 'Prorrogação', FALSE, 'ir_prorrogacao();');
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

	$head = array( 
		'De - Gerência/Usuário',
		'Para - Gerência/Usuário',
		'Justificativa',
		'Data'
	);

	$body = array();
	
	foreach($collection as $item)
	{
		$body[] = array(
			array($item['cd_divisao_inclusao'].' - '.$item['ds_usuario_inclusao'], 'text-align:left'),
			array($item['cd_divisao_reencaminhamento'].' - '.$item['ds_usuario_responsavel_reencaminhamento'], 'text-align:left'),
			array(nl2br($item['ds_justificativa_reencaminhamento']), 'text-align:justify'),
			$item['dt_inclusao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_conceito_box', 'Coneceito da Tela');
			echo form_default_row('conceito', 'Reencaminhamento:', 'Ao analisar a reclamação e verificar que não é de sua competência, deverá reencaminhar para a área/responsável, descrevendo uma justificativa.');
		echo form_end_box('default_conceito_box');
		echo form_start_box('default_reclamacao_box', 'Reclamação');
			echo form_default_row('numero', 'Número:', $reclamacao['cd_reclamacao']);

			if(intval($reclamacao['cd_usuario_responsavel']) > 0)
			{
				echo form_default_row('dt_prazo_acao', 'Dt. Prazo Ação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo_acao'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao_acao']) != '')
				{
					echo form_default_row('dt_prorrogacao_acao', 'Dt. Prorrogação Ação:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao_acao'].'</span>');
				}

				echo form_default_row('dt_prazo', 'Dt. Prazo Classificação:', '<span class="label label-inverse">'.$reclamacao['dt_prazo'].'</span>');
				
				if(trim($reclamacao['dt_prorrogacao']) != '')
				{
					echo form_default_row('dt_prorrogacao', 'Dt. Prorrogação Classificação:', '<span class="label label-info">'.$reclamacao['dt_prorrogacao'].'</span>');
				}
			}
			
		echo form_end_box('default_reclamacao_box');
		echo form_open('ecrm/reclamacao/salvar_atendimento_reencaminhamento');
			echo form_start_box('default_box_reencaminhamento', 'Reencaminhamento');
				echo form_default_hidden('numero', '', $row['numero']);
				echo form_default_hidden('ano', '', $row['ano']);
				echo form_default_hidden('tipo', '', $row['tipo']);
				echo form_default_gerencia('cd_divisao_reencaminhamento', 'Gerência: (*)', $row['cd_divisao_reencaminhamento'], 'onchange="get_usuarios_reencaminhamento()"');
				echo form_default_dropdown('cd_usuario_responsavel_reencaminhamento', 'Responsável: (*)', $usuarios, $row['cd_usuario_responsavel_reencaminhamento'], 'onchange="get_ferias_reencaminhamento()"'); 
				echo form_default_textarea('ds_justificativa_reencaminhamento', 'Justificativa:');
			echo form_end_box('default_box_reencaminhamento');
			echo form_command_bar_detail_start();
				if((intval($row['cd_usuario_responsavel']) > 0) AND (($permissao['fl_acao_responsavel']) OR (gerencia_in(array('GP')) AND !$permissao['fl_encerrado'])))
				{
					echo button_save('Salvar');
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>