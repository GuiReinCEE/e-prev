<?php
set_title("RT E-mails - Conferência");
$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post('<?= site_url("ecrm/rt_conferencia/listar") ?>',
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
		    "RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
		    "DateTimeBR",
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
		ob_resul.sort(9, true);
	}

	$(function(){
		if(($("#dt_rt_inicio").val() == "") || ($("#dt_rt_fim").val() == ""))
		{
			$("#dt_rt_inicio_dt_rt_fim_shortcut").val("last30days");
			$("#dt_rt_inicio_dt_rt_fim_shortcut").change();
		}
		
		filtrar();
	});
</script>
<?php
$abas[] = array("aba_lista", "Lista", TRUE, "location.reload();");

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter(); 
		echo filter_cpf("cpf", "CPF");
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, TRUE);
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_date_interval("dt_rt_inicio", "dt_rt_fim", "Dt. Atualização:");
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(10);
echo aba_end();
$this->load->view('footer');
?>