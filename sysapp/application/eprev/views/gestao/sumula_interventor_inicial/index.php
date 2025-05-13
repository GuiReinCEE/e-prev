<?php
    set_title('S�mulas Interventor - Lista');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
    	$("#result_div").html("<?= loader_html() ?>");
    	
        $.post("<?=  site_url('gestao/sumula_interventor_inicial/listar') ?>",
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
    		"Number",
    		"CaseInsensitiveString",
    		"CaseInsensitiveString",
    		"DateBR",
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

    function novo()
    {
        location.href = "<?= site_url('gestao/sumula_interventor_inicial/cadastro') ?>";
    }

    $(function(){
    	filtrar();
    })
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    if(gerencia_in(array('SG')))
    {
        $config['button'][] = array('Nova S�mula', 'novo()');
    }
    else
    {
        $config = array();
    }
    
    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter();
            echo filter_integer('nr_sumula_interventor', 'N� da S�mula:');
            echo filter_date_interval('dt_sumula_ini', 'dt_sumula_fim', 'Data:');
            echo filter_date_interval('dt_divulgacao_ini', 'dt_divulgacao_fim', 'Dt Divulga��o:');
        echo form_end_box_filter();
    	echo '<div id="result_div"></div>';
    	echo br();
    echo aba_end();

    $this->load->view('footer'); 
?>