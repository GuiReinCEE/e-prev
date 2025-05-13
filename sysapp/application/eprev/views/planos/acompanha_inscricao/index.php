<?php
	set_title('Rel. Acompanha Inscrição');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('planos/acompanha_inscricao/listar') ?>",
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateBR",
			"DateBR",
			"NumberFloatBR",
			"DateBR",
			"DateBR",
			"Number",
			"Number",
			"Number",
			"Number"
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

		if($('#dt_solicitacao_ini').val() == '' || $('#dt_solicitacao_fim').val() == '')
		{
			$('#dt_solicitacao_ini_dt_solicitacao_fim_shortcut').val('currentYear');
			$('#dt_solicitacao_ini_dt_solicitacao_fim_shortcut').change();
		}
		
		
		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$arr = array(
		array('text' => 'Não', 'value' => 'N'),
		array('text' => 'Sim', 'value' => 'S')
	);


	$tipo_cliente = array(
		array('text' => 'Patrocinador', 'value' => 'P'),
		array('text' => 'Instituidor', 'value' => 'I')
	);


	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter('filter_bar', 'Filtros', false);
			echo filter_dropdown('cd_tipo_cliente', 'Tipo Empresa:', $tipo_cliente);
			#echo filter_plano_ajax('cd_plano', '', '', 'Empresa:', 'Plano:');
			echo filter_dropdown('cd_plano_empresa', 'Empresa:', $empresa);
			echo filter_dropdown('id_tipo_liquidacao', 'Forma PG:', $forma_pagamento);
			echo filter_date_interval('dt_solicitacao_ini', 'dt_solicitacao_fim', 'Dt Solicitação:');
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Inclusão:');
			echo filter_date_interval('dt_confirma_ini', 'dt_confirma_fim', 'Dt Confirma:');
			echo filter_date_interval('dt_cobranca_ini', 'dt_cobranca_fim', 'Dt Cobrança:');
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio:');
			echo filter_date_interval('dt_dig_ingresso_ini', 'dt_dig_ingresso_fim', 'Dt Dig Ingresso:');
			echo filter_date_interval('dt_ingresso_ini', 'dt_ingresso_fim', 'Dt Ingresso:');
			echo filter_dropdown('fl_participante', 'Participante:', $arr);
			echo filter_dropdown('fl_ingresso', 'Ingressou:', $arr);
			echo filter_dropdown('fl_cancela_inscricao', 'Inscrição Cancelada:', $arr);
			echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), 'RE:', '', TRUE, TRUE );
			echo filter_text('nome', 'Nome:', '', "style='width:300px;'" );
			echo filter_cpf('cpf_mf', 'CPF:');
		echo form_end_box_filter();
		
		echo form_start_box('legenda_box', 'Legenda');
			echo form_default_row('', '<b>FORMA PG:</b>', '<span style="font-size: 80%">Forma de pagamento escolhida na inscrição<br/>BDL: bloqueto ou arrecadação<br/>BCO: desconto em conta corrente<br/>FOL: desconto em folha do empregador<br/>FLT: desconto em folha de terceiro na patrocinadora (hoje somente no plano Família)<br/>CHQ: pagamento da primeira contribuição através de cheque<br/>DEP: pagamento da primeira contribuição através depósito na conta da Fundação</span>');
			echo form_default_row('', '<b>DT SOLICITAÇÃO:</b>', '<span style="font-size: 80%">Data de assinatura do formulário de inscrição</span>');
			echo form_default_row('', '<b>DT ENVIO GP:</b>', '<span style="font-size: 80%">Última data que GE informou que enviou o formulário de inscrição para a GP</span>');
			echo form_default_row('', '<b>DT RECEB:</b>', '<span style="font-size: 80%">Data que o formulário foi recebido pelo cadastro</span>');
			echo form_default_row('', '<b>CALENDÁRIO CAD :</b>', '<span style="font-size: 80%">Período de inclusão de inscrições da GP cadastro </span>');
			echo form_default_row('', '<b>DT INCLUSÃO:</b>', '<span style="font-size: 80%">Data que o cadastro incluiu no sistema eletro a inscrição</span>');
			echo form_default_row('', '<b>DT CONFIRMA:</b>', '<span style="font-size: 80%">Data que o cadastro liberou para a geração a inscrição</span>');
			echo form_default_row('', '<b>DT COBRANÇA:</b>', '<span style="font-size: 80%">Data que a contribuição foi gerada</span>');
			echo form_default_row('', '<b>DT ENVIO:</b>', '<span style="font-size: 80%">Data que o financeiro enviou para o participante pagar</span>');
			echo form_default_row('', '<b>DT DIG INGRESSO:</b>', '<span style="font-size: 80%">Data que foi informado o ingresso do participante (digitação da informação)</span>');
			echo form_default_row('', '<b>DT INGRESSO:</b>', '<span style="font-size: 80%">Data oficial de ingresso no plano</span>');
			echo form_default_row('', '<b>VL PRIMEIRA PAGAMENTO:</b>', '<span style="font-size: 80%">Valor do primeira pagamento (apenas para instituidor)</span>');
			echo form_default_row('', '<b>DT DESLIGA:</b>', '<span style="font-size: 80%">Data de desligamento do plano</span>');
			echo form_default_row('', '<b>DT CANCELA:</b>', '<span style="font-size: 80%">Data de cancelamento da inscrição (desistência)</span>');
			echo form_default_row('', '<b>QT DIA CADASTRO:</b>', '<span style="font-size: 80%">Tempo decorrido entre a SOLICITAÇÃO (dt solicitação) e a LIBERAÇÃO (dt confirma) para geração </span>');
			echo form_default_row('', '<b>QT DIA COBRANÇA:</b>', '<span style="font-size: 80%">Tempo decorrido entre a SOLICITAÇÃO (dt solicitação) e a GERAÇÃO (dt cobrança)</span>');
			echo form_default_row('', '<b>QT DIA ENVIO:</b>', '<span style="font-size: 80%">Tempo decorrido entre a SOLICITAÇÃO (dt solicitação) e o ENVIO PARA PAGAMENTO (dt envio)</span>');
			echo form_default_row('', '<b>QT DIA INGRESSO:</b>', '<span style="font-size: 80%">Tempo decorrido entre a SOLICITAÇÃO (dt solicitação) e o a informação do INGRESSO (dt dig ingresso)</span>');
		echo form_end_box("legenda_box");	
		
		echo '<div id="result_div"></div>';
		echo br(10);
	echo aba_end();
	$this->load->view('footer');
?>