<?php
set_title("Contribuição Programada");
$this->load->view("header");
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post('<?= site_url("ecrm/contribuicao_programada/listar") ?>',
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
		    "RE",
			"CaseInsensitiveString",
			"NumberFloat",
			"NumberFloat",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString"
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
		ob_resul.sort(5, true);
	}

	$(function(){
		$("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").val("currentMonth");
		$("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").change();
		filtrar();
	});
</script>
<?php
$abas[] = array("aba_lista", "Lista", TRUE, "location.reload();");

$arr_sim_nao[] = array('value' => 'S', 'text' => 'Sim');
$arr_sim_nao[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter(); 
		echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome'), "Participante:", Array(), TRUE, TRUE);
		echo filter_text('nome', 'Nome:','','style="width: 350px;"');
		echo filter_date_interval("dt_solicitacao_ini", "dt_solicitacao_fim", "Dt. Solicitação:");
		echo filter_date_interval("dt_inicio_ini", "dt_inicio_fim", "Dt. Início:");
		echo filter_dropdown('fl_cancelado', 'Cancelado:', $arr_sim_nao, array('N'));
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(10);
echo aba_end();
$this->load->view('footer');
?>