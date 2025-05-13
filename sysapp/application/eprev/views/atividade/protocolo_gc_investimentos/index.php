<?php
    set_title('Controle de Documentos GC Investimento');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('atividade/protocolo_gc_investimentos/listar') ?>",
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
            null,
            "CaseInsensitiveString",
            "DateTimeBR",
            "DateTimeBR",
            "DateBR",
            "DateBR",
			"DateTimeBR",
            "DateTimeBR"
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
        ob_resul.sort(4, true);
    }
					
    function novo()
    {
        location.href = "<?= site_url('atividade/protocolo_gc_investimentos/cadastro') ?>";
    }

    $(function(){
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Novo Controle', 'novo()');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter(); 
            echo filter_date_interval('dt_envio_gc_ini', 'dt_envio_gc_fim', 'Dt Envio:');
            echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Dt Recebido:');
            echo filter_date_interval('dt_envio_sg_ini', 'dt_envio_sg_fim', 'Dt Envio SG:');
            echo filter_date_interval('dt_expedicao_ini', 'dt_expedicao_fim', 'Dt Expedição:');
            echo filter_date_interval('dt_encerrar_ini', 'dt_encerrar_fim', 'Dt Encerrado:');
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br();
    echo aba_end();

    $this->load->view('footer');
?>