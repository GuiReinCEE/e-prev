<?php
	set_title('Solicitação de Número de RDS');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('gestao/controle_rds_solicitacao/listar') ?>",
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
		    "CaseInsensitiveString",
			"DateBR"
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
		ob_resul.sort(0, true);
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/controle_rds_solicitacao/cadastro') ?>";
	}

    $(function()
    {
    	if(<?= intval($nr_ano) ?> > 0)
        {
            $("#nr_ano").val("<?= $nr_ano ?>");
        }
        else if($("#nr_ano").val() == "")
        {
            $("#nr_ano").val("<?= date('Y') ?>");
        }

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Número de RDS', 'ir_cadastro();');

	echo aba_start($abas);
		echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', FALSE);
        	echo filter_integer('nr_ano', 'Ano: ');
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>

