<?php
	set_title('Convênios de adesão');
	$this->load->view('header');
?>
<script>
	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/convenio_adesao/cadastro') ?>";
	}

    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('gestao/convenio_adesao/listar') ?>",
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
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
		ob_resul.sort(0, false);
	}

	$(function (){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Novo Convênio de Adesão', 'ir_cadastro();');

	echo aba_start($abas);
	    echo form_list_command_bar(gerencia_in(array('GC')) ? $config : array());
		echo form_start_box_filter(); 
			echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');
	    echo form_end_box_filter();
	    echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	echo br();
	$this->load->view('footer');
?>