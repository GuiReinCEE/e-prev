<?php
set_title('Formulário Fatca');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/formulario_fatca/listar') ?>",
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
		    "RE",
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
		ob_resul.sort(2, true);
	}

	$(function(){
		filtrar();
		$("#dt_ini_dt_fim_shortcut").val("currentMonth");
		$("#dt_ini_dt_fim_shortcut").change();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$conf = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante');

	$valores_re = array('cd_empresa' => $cd_empresa, 'cd_registro_empregado' => $cd_registro_empregado, 'seq_dependencia' => $seq_dependencia);
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_participante($conf, "RE:", $valores_re, FALSE, TRUE, TRUE); 	
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Inclusão:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>