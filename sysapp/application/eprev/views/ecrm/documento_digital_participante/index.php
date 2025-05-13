<?php
	set_title('Documento Digital Participante');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/documento_digital_participante/listar') ?>",
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
			"DateTimeBR",
			'RE',
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
		    "CaseInsensitiveString", 
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
		ob_resul.sort(0, true);
	}

	$(function(){
		$("#fl_status").val("RUNNING");
		$("#fl_status").change();	
		
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val("last60days");
		$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").change();
		
		filtrar();
	});

</script>

<?php
$ar_status = Array(
Array('text' => 'Em processo de assinatura', 'value' => 'RUNNING'),
Array('text' => 'Finalizado', 'value' => 'CLOSED'),
Array('text' => 'Cancelado', 'value' => 'CANCELED')
);
/*
RUNNING => Documento em processo de assinatura
CANCELED => Documento cancelado
CLOSED => Documento finalizado
*/		

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			
			echo filter_dropdown('fl_status', 'Status:', $ar_status);
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Inclusão:');
			
		echo form_end_box_filter();
		echo '<div id="result_div" style="text-align: center;"></div>';
		echo br(5);
	echo aba_end();

$this->load->view('footer');
?>