<?php
set_title('Pagamento de Cheque');
$this->load->view('header');
?>
<script>
	$(function(){
	   filtrar(); 
	});
	
	function filtrar()
	{
		$('#result_div').html("<?=loader_html()?>");

		$.post('<?=site_url('atividade/pagamento_cheque/listar')?>',
		$('form').serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTime',
			'Numeric',
			'DateTimeBR',
			'CaseInsensitiveString'
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
	
</script>

<?php 
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_status[] = array('value' => 'GB', 'text' => 'Aguardando Benefício');
$arr_status[] = array('value' => 'C', 'text' => 'Confirmado');

echo aba_start( $abas );
    echo form_list_command_bar(array());
	echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/Número:');
		echo filter_cpf('cpf', 'CPF:');
		echo filter_text('nome', 'Nome Reclamante:', '', 'style="width:350px;"');
		echo filter_dropdown('ano_nr_processo', 'Nr Processo:', $arr_processo);
		echo filter_dropdown('cd_calculo_irrf_correspondente', 'Nr Correspondente:', $arr_correspondente);
		echo filter_date_interval('dt_pagamento_ini', 'dt_pagamento_fim', 'Dt Pagamento:');
		echo filter_dropdown('fl_status', 'Status:', $arr_status);
		echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'Participante:', false, true, false);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer'); 
?>