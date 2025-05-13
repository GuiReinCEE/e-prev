<?php
	set_title('Controle de Reclamações - Parecer Comitê');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('ecrm/reclamacao/parecer_comite_listar'); ?>",
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
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			null,
			"DateTimeBR",
			"CaseInsensitiveString",
			null
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
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_integer('numero', 'Número:');
			echo filter_integer('ano', 'Ano:');
			echo filter_dropdown('fl_status', 'Status:', $status);
			echo filter_date_interval('dt_parecer_final_ini', 'dt_parecer_final_fim', 'Dt. Parecer Final:');
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>