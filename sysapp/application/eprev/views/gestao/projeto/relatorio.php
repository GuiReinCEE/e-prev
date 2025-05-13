<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/projeto') ?>";
	}
	
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/projeto/relatorio_listar') ?>",
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
		    "NUmber",
		    "CaseInsensitiveString",
			null,
		    "DateBR",
		    "DateBR",
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
		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_lista_relatorio', 'Lista Relatório', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
		
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	
	$this->load->view('footer');
?>