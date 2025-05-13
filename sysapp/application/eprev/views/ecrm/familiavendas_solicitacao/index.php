<?php
	set_title('Família Vendas');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/familiavendas_solicitacao/listar') ?>",
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
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "Number",
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(1, true);
	}

	$(function(){
		filtrar();
	});
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$tp_status = array(
		array('value' => 'S', 'text' => 'Solicitado'),
		array('value' => 'A', 'text' => 'Em análise'),
		array('value' => 'C', 'text' => 'Cancelado'),
		array('value' => 'O', 'text' => 'Confirmado')
	);

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_text('nr_protocolo','Nr. Protocolo:','');
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', ' Dt. Solicitação:');
			echo filter_dropdown('tp_status','Status:',$tp_status);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>