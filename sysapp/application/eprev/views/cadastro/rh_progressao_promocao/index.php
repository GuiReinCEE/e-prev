<?php
	set_title('Sistema de Avaliação - Progressão/Promoção');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/rh_progressao_promocao/listar') ?>",
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
			null,
			"CaseInsensitiveString",
			"DateBR",
			"CaseInsensitiveString",
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
		ob_resul.sort(2, false);
	}
	
	$(function(){
		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

$this->load->view('footer');
?>