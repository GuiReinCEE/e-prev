<?php
	set_title('Relatório de Reclamações');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/reclamacao') ?>";
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('ecrm/reclamacao/relatorio_listar'); ?>",
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
			"DateBR",
			"DateBR",
			"DateTimeBR",
			"CaseInsensitiveString",
			null,
			"DateBR"
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
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');

	$situacao = array(
		array('value' => 'TFP', 'text' => 'Tratadas fora do prazo'),
		array('value' => 'NTR', 'text' => 'Não tratadas'),
		array('value' => 'NCC', 'text' => 'Não confirmadas pelo Comitê')
	);
			
	echo aba_start($abas);
		echo form_list_command_bar(array());	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Cadastro:', calcular_data('','1 year'), date('d/m/Y'));
			echo filter_dropdown('fl_situacao', 'Situação:', $situacao);
			echo filter_dropdown('cd_reclamacao_retorno_classificacao', 'Classificação:', $retorno_classificacao);
			echo filter_dropdown('cd_reclamacao_assunto', 'Assunto:', $assunto);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>