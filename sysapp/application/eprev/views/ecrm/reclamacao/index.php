<?php
	set_title('Controle de Reclamações');
	$this->load->view('header');
?>
<script>
	function ir_relatorio()
	{
		location.href = "<?= site_url('ecrm/reclamacao/relatorio') ?>";
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('ecrm/reclamacao/listar'); ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
            "CaseInsensitiveString",
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateTimeBR",
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
		ob_resul.sort(0, true);
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/reclamacao/cadastro') ?>";
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');

	$config['button'][] = array('Nova Reclamação ou Sugestão', 'novo();');

	$situacao = array(
		array('value' => 'A', 'text' => 'Abertas'),
		array('value' => 'E', 'text' => 'Encerradas'),
		array('value' => 'T', 'text' => 'Atrasadas'),
		array('value' => 'C', 'text' => 'Canceladas')
	);

	$tipo = array(
		array('value' => 'R', 'text' => 'Reclamação'),
		array('value' => 'S', 'text' => 'Sugestão')
	);

	$tipo_empresa = array(
		array('value' => 'I', 'text' => 'Instituidor'),
		array('value' => 'P', 'text' => 'Patrociandora')
	);
			
	echo aba_start($abas);
		echo form_list_command_bar($config);	
		echo form_start_box_filter('filter_bar', 'Filtros', false);
			echo filter_integer('numero', 'Número:');
			echo filter_integer('ano', 'Ano:');
			echo filter_dropdown('tipo', 'Tipo:', $tipo);
			echo filter_dropdown('fl_situacao', 'Situação:', $situacao);	
			echo filter_dropdown('fl_prorrogada', 'Prorrogada:', $dropdown_sim_nao);		
			echo filter_dropdown('cd_empresa_patr', 'Empresa:', $empresa);
			echo filter_dropdown('fl_tipo_cliente', 'Tipo Empresa:', $tipo_empresa);
			echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante:', $participante, TRUE, TRUE );	
			echo filter_text('nome', 'Nome:', '', 'style="width:500px;"');
			echo filter_dropdown('cd_plano', 'Plano:', $planos);	
			echo filter_dropdown('fl_participante', 'Participante:', $dropdown_sim_nao);	
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Cadastro:', calcular_data('','1 year'), date('d/m/Y'));
			echo filter_date_interval('dt_atendimento_ini', 'dt_atendimento_fim', 'Dt. Encaminhado:');
			echo filter_usuario_ajax(array('cd_divisao','cd_usuario_responsavel'), '', '', 'Responsável:', 'Gerência:');
			echo filter_date_interval('dt_prazo_acao_ini', 'dt_prazo_acao_fim', 'Dt. Prazo Ação:');
			echo filter_date_interval('dt_prazo_classificacao_ini', 'dt_prazo_classificacao_fim', 'Dt. Prazo Classificação:');
			
			echo filter_date_interval('dt_encerrado_ini', 'dt_encerrado_fim', 'Dt. Encerrado:');
			echo filter_dropdown('cd_reclamacao_retorno_classificacao', 'Classificação:', $retorno_classificacao);
			echo filter_dropdown('cd_reclamacao_programa', 'Programa:', $programa);
			echo filter_dropdown('cd_reclamacao_assunto', 'Assunto:', $assunto);
			echo filter_dropdown('cd_usuario_inclusao', 'Aberta por:', $usuario_inclusao);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>