<?php
	set_title('Acesso por Empresa/Plano');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('planos/app/listar') ?>",
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
		ob_resul.sort(2, true);
	}

	$(function() {

		if($("#dt_ini").val() == "")
		{
			$("#dt_ini").val("28/02/2018")
		}

		if($("#dt_fim").val() == '')
		{
			$("#dt_fim").val("<?= date('d/m/Y') ?>")
		}

        filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config = array();

	echo aba_start($abas);
	    echo form_list_command_bar($config);
	    echo form_start_box_filter();
	    	echo filter_date_interval('dt_ini', 'dt_fim', 'Data de Acesso:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>