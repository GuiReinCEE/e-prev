<?php
	set_title('Abaixo Assinado');
	$this->load->view('header');
?>
<script>
    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/cadastro') ?>";
    }

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('ecrm/abaixo_assinado/listar'); ?>",
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
            "Number",
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
        ob_resul.sort(0, true);
    }
    
    $(function(){
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config = array();

    if($fl_permissao)
    {
        $config['button'][] = array('Novo Abaixo Assinado', 'ir_cadastro();');
    }

    echo aba_start($abas);
    echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
        echo form_default_integer_ano('nr_ano','nr_numero', 'Ano/N°:');
        echo filter_participante(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome'), 'Participante:', '', TRUE, FALSE);
        echo filter_date_interval('dt_protocolo_ini', 'dt_protocolo_fim', 'Dt. Protocolo:');

        echo filter_date_interval('dt_limite_retorno_ini', 'dt_limite_retorno_fim', 'Dt. Limite Retorno:');

        echo filter_date_interval('dt_retorno_ini', 'dt_retorno_fim', 'Dt. Retorno:');
        echo filter_dropdown('fl_retorno', 'Retorno:', $drop); 
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>