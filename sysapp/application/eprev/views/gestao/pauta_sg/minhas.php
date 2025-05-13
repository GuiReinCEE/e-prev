<?php
	set_title('Pauta SG - Minhas');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/pauta_sg/minhas_listar') ?>",
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
		    "Number",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "Number",
		    "Number",
		    "DateTime"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(4, true);
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_integer('nr_ata', 'Nº da Ata:');
			echo filter_dropdown('fl_sumula', 'Colegiado:', $sumula);
			echo filter_dropdown('fl_tipo_reuniao', 'Tipo Reunião:', $tipo_reuniao);
			echo filter_date_interval('dt_pauta_sg_ini', 'dt_pauta_sg_fim', 'Dt. Reunião:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(5);
	echo aba_end();

	$this->load->view('footer');
?>