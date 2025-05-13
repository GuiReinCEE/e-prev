<?php
	set_title('Minhas Atividades');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('atividade/minhas/listar') ?>",
		{
			status                        : $('#status').val(),
			status_aguardando             : (($('#aguardando').attr('checked')) ? 'S' : 'N'),
			status_em_andamento           : (($('#em_andamento').attr('checked')) ? 'S' : 'N'),
			status_encerrado              : (($('#encerrado').attr('checked')) ? 'S' : 'N'),
			status_em_teste               : (($('#em_teste').attr('checked')) ? 'S' : 'N'),
			status_aguardando_definicao   : (($('#aguardando_definicao').attr('checked')) ? 'S' : 'N'),
			status_aguardando_usuario     : (($('#aguardando_usuario').attr('checked')) ? 'S' : 'N'),
			feitas                        : (($('#minhas_feitas').attr('checked')) ? 'S' : 'N'),
			recebidas                     : (($('#minhas_recebidas').attr('checked')) ? 'S' : 'N'),
			fl_cronograma                 : $('#fl_cronograma').val(),
			fl_prioridade                 : $('#fl_prioridade').val(),
			nr_prioridade_ini             : $('#nr_prioridade_ini').val(),
			nr_prioridade_fim             : $('#nr_prioridade_fim').val(),
			dt_solicitacao_inicio         : $('#dt_solicitacao_inicio').val(),
			dt_solicitacao_fim            : $('#dt_solicitacao_fim').val(),
			dt_envio_inicio               : $('#dt_envio_inicio').val(),
			dt_envio_fim                  : $('#dt_envio_fim').val(),
			dt_conclusao_inicio           : $('#dt_conclusao_inicio').val(),
			dt_conclusao_fim              : $('#dt_conclusao_fim').val(),
			divisao_solicitante           : $('#divisao_solicitante').val(),
			projeto                       : $('#projeto').val(),
			cd_tipo_solicitacao           : $('#cd_tipo_solicitacao').val(),
			cd_solicitante                : $('#cd_solicitante').val(),
			cd_atendente                  : $('#cd_atendente').val(),
			descricao                     : $('#descricao').val(),
			cd_empresa                    : $('#cd_empresa').val(),
			cd_registro_empregado         : $('#cd_registro_empregado').val(),
			seq_dependencia               : $('#seq_dependencia').val(),
			numero                        : $('#numero').val(),
			fl_balanco_gi                 : $('#fl_balanco_gi').val(),
			cd_atividade_cronograma_grupo : $('#cd_atividade_cronograma_grupo').val(),
			fl_gerente_view               : $('#fl_gerente_view').val(),
			cd_gerencia_atendente         : $('#cd_gerencia_atendente').val(),
			cd_atividade_classificacao    : $('#cd_atividade_classificacao').val(),
			fl_juridico_emprestimo        : $('#fl_juridico_emprestimo').val(),
			fl_administrativo             : $('#fl_administrativo').val()
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
			"Number",
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"Number",
			"CaseInsensitiveString",
			null,
			"Number",
			null,
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
			"RE"
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
		
		if($("#fl_prioridade").val() == "S")
		{
			ob_resul.sort(9, false);
		}
		else
		{
			ob_resul.sort(0, true);
		}
	}

	function ir()
	{
		if($('#numero').val() != "")
		{
			$.post('<?= site_url('atividade/minhas/buscarAtividade') ?>',
			{
				cd_atividade : $('#numero').val()
			},
			function(data)
			{
				if(data.url != "")
				{
					location.href = data.url;
				}
			}, 'json');	
		}
		else
		{
			alert("Informe o número da atividade");
			$('#numero').focus();
		}	
	}

	function ir_tarefa()
	{
		location.href = "<?= site_url('atividade/tarefa') ?>";
	}

	$(function() {
		filtrar();
		
		<?php
			if($this->session->userdata('divisao') == 'GTI')
			{
				echo "$('#fl_balanco_gi_row').show()";
			}	
			else
			{
				echo "$('#fl_balanco_gi_row').hide()";
			}
		?>
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	if($this->session->userdata('divisao') == 'GTI')
	{
		$abas[] = array('aba_tarefa', 'Tarefas', FALSE, 'ir_tarefa();');
	}

	$status = '';
	$i = 0;

	foreach($filtros['status'] as $item)
	{
		$i++;

		$status .= form_checkbox(array('id' => $item['id']), $item['value'], $item['checked']).'<label for="'.$item['id'].'">'.$item['text'].'</label><br/>';
	}

	$minhas_feitas = resgatar_filtro('feitas');
	$minhas_recebidas = resgatar_filtro('recebidas');

	$minhas_feitas = ($minhas_feitas == 'S' || $minhas_feitas == '');
	$minhas_recebidas = ($minhas_recebidas == 'S' || $minhas_recebidas == '');

	$minhas = form_checkbox(array('id'=>'minhas_feitas'), 's', $minhas_feitas).'<label for="minhas_feitas">Solicitações feitas</label><br/>';
	$minhas .= form_checkbox(array('id'=>'minhas_recebidas'), 's', $minhas_recebidas).'<label for="minhas_recebidas">Solicitações recebidas</label><br/>';

	$ar_cronograma = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
	#echo filter_dropdown('fl_cronograma', 'Cronograma: ', $ar_cronograma);
	#echo filter_dropdown('fl_prioridade', 'Prioridade Consenso: ', $ar_cronograma);
	#echo filter_dropdown( 'cd_atividade_cronograma_grupo', 'Grupo Cronograma: ', $grupos );

	$cd_empresa=resgatar_filtro('cd_empresa');
	$cd_registro_empregado=resgatar_filtro('cd_registro_empregado');
	$seq_dependencia=resgatar_filtro('seq_dependencia');
	$cd_empresa=($cd_empresa==-1)?"":$cd_empresa;
	$cd_registro_empregado=($cd_registro_empregado==0)?"":$cd_registro_empregado;
	$seq_dependencia=($seq_dependencia==-1)?"":$seq_dependencia;

	$ar_flag[] = array('text' => 'Sim', 'value' => 'S');
	$ar_flag[] = array('text' => 'Não', 'value' => 'N');

	$ar_gerente_view = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N'));

	$ar_juridico_emprestimo = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N'));

	$ar_administrativo = Array(Array('text' => 'Selecione', 'value' => ''),Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N'));

	echo aba_start($abas);

		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', false);

			echo filter_text('numero', 'Número: ');
			echo form_default_row('', '', "<input type='button' onclick='ir()' class='botao' value='Buscar' />");

			echo form_default_row('blank_row', '&nbsp', "<br>");
			echo filter_text('descricao', 'Descrição:','','style="width: 500px;"');

			echo form_default_row('blank_row', '&nbsp', "<br>");
			echo form_default_row('status_row', 'Status: ', $status);
			echo form_default_row('blank_row', '&nbsp', "<br>");
			echo form_default_row('minhas_row', 'Minhas: ', $minhas);
			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo filter_hidden('fl_cronograma', '', '');
			echo filter_hidden('fl_prioridade', '', '');
			echo filter_hidden('cd_atividade_cronograma_grupo', '', '');

			echo filter_integer_interval('nr_prioridade_ini', 'nr_prioridade_fim', 'Prioridade:');

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo filter_date_interval('dt_solicitacao_inicio', 'dt_solicitacao_fim', 'Dt. Solicitação:');
			echo filter_date_interval('dt_envio_inicio', 'dt_envio_fim', 'Dt. Envio Teste:');
			echo filter_date_interval('dt_conclusao_inicio', 'dt_conclusao_fim', 'Dt. Conclusão:');

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo filter_dropdown('projeto', 'Projeto: ', $projetos_dd);
			echo filter_dropdown('cd_tipo_solicitacao', 'Tipo solicitação:', $ar_tipo_solicitacao);
			echo filter_dropdown('cd_atividade_classificacao', 'Classificação:', $classificacao);

			echo form_default_row('blank_row', '&nbsp', "<br>");
			echo filter_dropdown('divisao_solicitante', 'Gerência Solicitante:', $divisao_solicitante_dd);
			echo filter_dropdown('cd_solicitante', 'Solicitante:', $solicitante_dd );
			echo filter_dropdown('cd_gerencia_atendente', 'Gerência Atendente:', $arr_area_atendente);
			echo filter_dropdown('cd_atendente', 'Atendente:', $atendente_dd);

			echo form_default_row('blank_row', '&nbsp', "<br>");

			echo form_default_participante(
				array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 
				'Participante (EMP/RE/SEQ):', 
				array('cd_empresa' => $cd_empresa, 'cd_registro_empregado' => $cd_registro_empregado, 'seq_dependencia' => $seq_dependencia), 
				TRUE, 
				FALSE 
			);

			echo form_default_row('blank_row', '&nbsp', "<br>");
			echo filter_dropdown('fl_balanco_gi', 'Balanço Anual - GTI:', $ar_flag);

			if(($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('indic_13') == 'S'))
			{
				echo form_default_row('blank_row', '&nbsp', "<br>");
				echo filter_dropdown('fl_gerente_view', 'Atividades da Gerência:', $ar_gerente_view, array('fl_gerente_view' => $fl_gerente_view));
			}

			if(($this->session->userdata('divisao') == 'GJ')  OR ($this->session->userdata('codigo') == 251))
			{
				echo form_default_row('blank_row', '&nbsp', "<br>");
				echo filter_dropdown('fl_juridico_emprestimo', 'Atividades GJ Empréstimos:', $ar_juridico_emprestimo, array('fl_juridico_emprestimo' => $fl_juridico_emprestimo));
			}

			if(($this->session->userdata('divisao') == 'GJ')  OR ($this->session->userdata('codigo') == 251))
			{
				echo form_default_row('blank_row', '&nbsp', "<br>");
				echo filter_dropdown('fl_administrativo', 'Atividades Administrativo:', $ar_administrativo, array('fl_administrativo' => $fl_administrativo));
			}
			

		echo form_end_box_filter();

		echo '<div id="result_div"></div>';

		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>