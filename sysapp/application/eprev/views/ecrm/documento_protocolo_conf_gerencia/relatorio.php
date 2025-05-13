<?php 
	set_title('Conferência de Documentos - Relatório');
	$this->load->view('header'); 
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('ecrm/documento_protocolo_conf_gerencia/listar_relatorio') ?>",
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
	    	"DateTimeBR",
	    	"CaseInsensitiveString",
	    	null,
			"Number",
			"Number",
			"Number",
			"Number",
			"Number",
			"Number",
			"Number"
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

	function gerar_pdf()
	{
		filter_bar_form.method = "post";
        filter_bar_form.action = '<?= site_url("ecrm/documento_protocolo_conf_gerencia/gerar_pdf") ?>';
        filter_bar_form.target = "_blank";
        filter_bar_form.submit();
	}

	$(function(){

		if($('#ano_referencia').val() == '')
		{ 
			$('#ano_referencia').val('<?= date('Y') ?>')
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Gerar PDF', 'gerar_pdf();');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
        	echo filter_mes('mes_referencia', 'Mês:');
        	echo filter_dropdown('ano_referencia', 'Ano:', $ano);
        	echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>