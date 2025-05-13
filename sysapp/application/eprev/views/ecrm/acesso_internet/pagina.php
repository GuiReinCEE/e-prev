<?php
set_title('Acessos ao site');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
	    $("#result_div").html("<?= loader_html() ?>");

	    $.post('<?= site_url("ecrm/acesso_internet/pagina_listar") ?>',
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
			'CaseInsensitiveString',
			'Number'
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
		ob_resul.sort(1, true);
	}

	function ir_periodo()
	{
		location.href = '<?= site_url('ecrm/acesso_internet') ?>';
	}

	function ir_aba_pagina()
	{
		location.href = '<?= site_url('ecrm/acesso_internet/pagina') ?>';
	}

	$(function(){
		filtrar()
	});
</script>

<?php
$abas[] = array('aba_lista', 'Por Período', FALSE, 'ir_periodo();');
$abas[] = array('aba_pagina', 'Por Página', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();

	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_date_interval('dt_ini', 'dt_fim', 'Data');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();

$this->load->view('footer');
?>