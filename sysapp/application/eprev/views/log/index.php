<?php
	set_title('Logs de Jobs Postgres');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('log/listar') ?>",
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
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			null, 
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
		if($("#dt_ini").val() == "")
        {
            $("#dt_ini").val("<?= date('d/m/Y') ?>");
            $("#dt_fim").val("<?= date('d/m/Y') ?>");
        }

		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
			echo form_default_dropdown('fl_status', 'Tipo de Log:', $status, $fl_status);
			echo form_default_date_interval('dt_ini', 'dt_fim', 'Dt. Erro:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();
	$this->load->view('footer');
?>