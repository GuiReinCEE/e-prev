<?php
    set_title('Demonstrativo de Resultados');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
    	$("#result_div").html("<?= loader_html(); ?>");
    		
        $.post("<?= site_url('gestao/demonstrativo_resultado/minhas_listar') ?>",
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
            "DateTimeBR",
            null,
            null,
            null,
            null,
            null
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

    function ir_anexo(cd_demonstrativo_resultado, cd_demonstrativo_resultado_mes)
    {
        location.href = "<?= site_url('gestao/demonstrativo_resultado/anexo') ?>/"+cd_demonstrativo_resultado+"/"+cd_demonstrativo_resultado_mes;
    }

    $(function(){
    	filtrar();
    })
</script>
<?php
    $abas[] = array('aba_minhas', 'Lista', TRUE, 'location.reload();');

    $fechamento = array(
        array('value' => 'N', 'text' => 'Não'),
        array('value' => 'S', 'text' => 'Sim')
    );

    echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter();
    		echo filter_integer_ano('nr_ano', 'nr_mes', 'Ano/Mês:');
            echo filter_dropdown('fl_fechamento', 'Fechado', $fechamento, 'N');
    	echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer'); 
?>