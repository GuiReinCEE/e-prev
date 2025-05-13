<?php
	set_title('Recursos Humanos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/rh/listar') ?>",
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
			null,
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString", 
			"Number", 
			"CaseInsensitiveString", 
			"DateBR"
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
		ob_resul.sort(3, false);
	}
	
	$(function(){
		if($("#fl_ativo").val() == "")
		{
			$("#fl_ativo").val("S");	
		}

		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$opcao = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
			echo filter_dropdown('fl_ativo', 'Ativo:', $opcao);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

$this->load->view('footer');
?>