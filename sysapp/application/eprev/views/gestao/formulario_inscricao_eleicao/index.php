<?php
	set_title('Inscrições Eleições');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/formulario_inscricao_eleicao/listar') ?>",
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
		    "Number",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(8, true);
	}

	$(function(){
		filtrar();
	});
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$ds_cargo = array(
		array('value' => 'DE', 'text' => 'Diretoria Executiva'), 
		array('value' => 'CF', 'text' => 'Conselho Fiscal'),
		array('value' => 'CD', 'text' => 'Conselho Deliberativo'),
		array('value' => 'CAP', 'text' => 'Comitê de Acompanhamento de Plano')
	);

	$ds_status = array(
		array('value' => 'AN', 'text' => 'Inscrição em andamento'), 
		array('value' => 'CA', 'text' => 'Inscrição cancelada'),
		array('value' => 'AP', 'text' => 'Inscrição aprovada'),
		array('value' => 'IN', 'text' => 'Inscrição impugnada')
	);


	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_integer('nr_ano', 'Ano Eleições', date("Y"));
			echo filter_dropdown('tp_cargo', 'Cargo:', $ds_cargo);
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', ' Dt. Inclusao:');
			echo filter_dropdown('fl_status','Status:', $ds_status);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>