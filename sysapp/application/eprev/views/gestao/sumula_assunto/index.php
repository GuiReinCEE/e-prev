<?php
	set_title('Súmula - Assuntos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('gestao/sumula_assunto/listar') ?>",
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
		ob_resul.sort(1, false);
	}

	$(function(){
	
		if(($("#dt_sumula_ini").val() == "") || ($("#dt_sumula_fim").val() == ""))
		{
			$("#dt_sumula_ini_dt_sumula_fim_shortcut").val("currentYear");
			$("#dt_sumula_ini_dt_sumula_fim_shortcut").change();
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
			echo filter_integer('nr_sumula', 'Nº Súmula:');
			echo filter_date_interval('dt_sumula_ini', 'dt_sumula_fim', 'Dt. Súmula:');
			echo filter_dropdown('fl_colegiado', 'Colegiado :', $colegiado);     
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>

