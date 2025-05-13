<?php
	set_title('Atividades com RE-d');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('atividade/participante/listar') ?>",
		{
			status_aguardando           : ($('#aguardando').attr('checked')) ? 'S' : 'N',
			status_em_andamento         : ($('#em_andamento').attr('checked')) ? 'S' : 'N',
			status_encerrado            : ($('#encerrado').attr('checked')) ? 'S' : 'N',
			status_em_teste             : ($('#em_teste').attr('checked')) ? 'S' : 'N',
			status_aguardando_definicao : ($('#aguardando_definicao').attr('checked')) ? 'S' : 'N',
			status_aguardando_usuario   : ($('#aguardando_usuario').attr('checked')) ? 'S' : 'N',
			feitas                      : ($('#minhas_feitas').attr('checked')) ? 'S' : 'N',
			recebidas                   : ($('#minhas_recebidas').attr('checked')) ? 'S' : 'N',
			dt_solicitacao_inicio       : $('#dt_solicitacao_inicio').val(),
		    dt_solicitacao_fim          : $('#dt_solicitacao_fim').val(),
		    dt_envio_inicio             : $('#dt_envio_inicio').val(),
		    dt_envio_fim                : $('#dt_envio_fim').val(),
		    dt_conclusao_inicio         : $('#dt_conclusao_inicio').val(),
		    dt_conclusao_fim            : $('#dt_conclusao_fim').val(),
		    dt_limite_doc_inicio        : $('#dt_limite_doc_inicio').val(),
		    dt_limite_doc_fim           : $('#dt_limite_doc_fim').val(),
		    divisao_solicitante         : $('#divisao_solicitante').val(),
		    projeto                     : $('#projeto').val(),
		    cd_tipo_solicitacao         : $('#cd_tipo_solicitacao').val(),
		    cd_solicitante              : $('#solicitante_dd').val(),
		    cd_atendente                : $('#atendente_dd').val(),
		    descricao                   : $('#descricao').val(),
		    cd_empresa                  : $('#cd_empresa').val(),
		    cd_registro_empregado       : $('#cd_registro_empregado').val(),
		    seq_dependencia             : $('#seq_dependencia').val(),
		    numero                      : $('#numero').val()

		},
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
			"RE",
			"CaseInsensitiveString",
			"Number",
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateBR"
		]);
		ob_resul.onsort = function()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(2, true);
	}

	function ir()
	{
		if($("#numero").val() != "")
		{
			$.post("<?= site_url('atividade/minhas/buscarAtividade') ?>",
			{
				cd_atividade : $("#numero").val()
			},
			function(data)
			{
				if(data.url != "")
				{
					location.href = data.url;
				}
			},
			'json');	
		}
		else
		{
			alert("Informe o número da atividade");
			
			$("#numero").focus();
		}	
	}

	$(function() {
        filtrar();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$status = '';
	
	$i = 0;
	
	foreach($filtros as $item)
	{
		$i++;
		$status .= form_checkbox(array('id' => $item['id']), $item['value'], $item['checked']).'<label for="'.$item['id'].'">'.$item['text'].'</label><br/>';
	}

	$minhas  = form_checkbox(array('id' => 'minhas_feitas'), 's', TRUE).'<label for="minhas_feitas">Solicitações feitas</label><br/>';
	$minhas .= form_checkbox(array('id' => 'minhas_recebidas'), 's', TRUE).'<label for="minhas_recebidas">Solicitações recebidas</label><br/>';

	$participante = array('cd_empresa' => $cd_empresa, 'cd_registro_empregado' => $cd_registro_empregado, 'seq_dependencia' => $seq_dependencia);

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', false);

			echo form_default_integer('numero', 'Número:');
			echo form_default_row('', '', "<input type='button' onclick='ir()' class='botao' value='Buscar'/>");

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo form_default_text('descricao', 'Descrição:', '', 'style="width:500px;"');

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo form_default_row('status_row', 'Status:', $status);

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo form_default_row('minhas_row', 'Minhas:', $minhas);

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo filter_date_interval('dt_solicitacao_inicio', 'dt_solicitacao_fim', 'Dt. Solicitação:');
			echo filter_date_interval('dt_envio_inicio', 'dt_envio_fim', 'Dt. Envio Teste:');
			echo filter_date_interval('dt_conclusao_inicio', 'dt_conclusao_fim', 'Dt. Conclusão:');
			echo filter_date_interval('dt_limite_doc_inicio', 'dt_limite_doc_fim', 'Dt. Limite Doc.:');

			echo form_default_row('blank_row', '&nbsp', '<br>');

			echo filter_dropdown('divisao_solicitante', 'Gerência solicitante:', $gerencia_solicitante);
			echo filter_dropdown('projeto', 'Projeto', $projetos);

			echo filter_dropdown('cd_tipo_solicitacao', 'Tipo solicitação:', $tipo_solicitacao);

			echo filter_dropdown('solicitante_dd', 'Solicitante:', $solicitante);
			echo filter_dropdown('atendente_dd', 'Atendente:', $atendente);

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo form_default_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante'), 'Participante (EMP/RE/SEQ):', $participante, TRUE, FALSE );

		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

	$this->load->view('footer');
?>