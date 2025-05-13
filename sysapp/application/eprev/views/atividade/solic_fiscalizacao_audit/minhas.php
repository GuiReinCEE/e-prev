<?php
	set_title('Registro de Solicita��es, Fiscaliza��es e Auditorias - Minhas');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('atividade/solic_fiscalizacao_audit/minhas_listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function ir_conferencia()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/minhas_conferencia') ?>";
    }

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Numeric",
			"CaseInsensitiveString",
			"CaseInsensitiveString", 
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
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, true);
	}

	$(function() {
        filtrar();
	});
</script>
<?php
    $abas[] = array('aba_minhas', 'Lista', TRUE, 'location.reload();');
    $abas[] = array('aba_minhas_conferencias', 'Confer�ncias', FALSE, 'ir_conferencia();');

    $status = array(
        array('value' => 'AR', 'text' => 'Aguardando Retorno'),
        array('value' => 'E',  'text' => 'Encaminhado'),
        array('value' => 'A',  'text' => 'Atendeu'),
        array('value' => 'NA', 'text' => 'N�o atendeu')
    );

	echo aba_start($abas);
        echo form_start_box_filter();
            echo form_default_integer_ano('nr_ano','nr_numero', 'Ano/N�:');
			echo filter_dropdown('cd_solic_fiscalizacao_audit_origem', 'Origem:', $origem);			    	
			echo filter_dropdown_optgroup('cd_solic_fiscalizacao_audit_tipo', 'Tipo:', $tipo); 	    	
            echo filter_date_interval('dt_prazo_ini', 'dt_prazo_fim', 'Dt. Prazo:');
            echo filter_dropdown('status', 'Status:', $status);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>