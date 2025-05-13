<?php
	set_title('Relato de Ocorrências');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('gestao/relato_ocorrencias/listar') ?>",
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
			"Date",
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
		ob_resul.sort(0, true);
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/relato_ocorrencias/cadastro') ?>";
	}
	
	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Relato de Ocorrências', 'ir_cadastro();');

	echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
        	echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão:'); 
        	echo filter_date_interval('dt_verificacao_ini', 'dt_verificacao_fim', 'Dt. Verificação:');
        	echo filter_dropdown('fl_verificado', 'Verificado:', $drop); 
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>