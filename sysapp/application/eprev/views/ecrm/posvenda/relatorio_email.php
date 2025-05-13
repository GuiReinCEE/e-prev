<?php
	set_title('Pós-Venda - Relatório de Emails');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>")
		
        $.post("<?= site_url('ecrm/posvenda/listar_relatorio_email') ?>",
		$("#filter_bar_form").serialize(),
        function(data)
        {
			$("#result_div").html(data)
            configure_result_table();
        });
	}

	function listar_result()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString", 
			"RE", 
			"CaseInsensitiveString",
			"CaseInsensitiveString"
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
   
    function ir_relatorio()
	{
		location.href = "<?= site_url('ecrm/posvenda/relatorio') ?>";
	}
    
    function ir_lista()
	{
		location.href = "<?= site_url('ecrm/posvenda') ?>";
	}

	$(function(){
		if($("#dt_ini").val() == '')
    	{
    		$("#dt_ini").val("<?= date('01/m/Y') ?>");
    	}

    	if($("#dt_fim").val() == '')
    	{
    		$("#dt_fim").val("<?= date('d/m/Y') ?>");
    	}

        filtrar();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_envia_email', 'Emails', TRUE, 'location.reload();');
	$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');

	echo aba_start($abas);
		echo form_list_command_bar();	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Envio:');
		echo form_end_box_filter();
	    echo '<div id="result_div"></div>';
	    echo br();
	echo aba_end();

	$this->load->view('footer');
?>
