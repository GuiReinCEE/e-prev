<?php
    set_title('Operacionalização de Novo Instituidor');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('planos/novo_instituidor/instituidor_listar') ?>",
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
            "DateTimeBR",
            "DateBR",
            "DateTimeBR",
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
        ob_resul.sort(3, false);
    }
					
    function cadastro()
    {
        location.href = "<?= site_url('planos/novo_instituidor/instituidor_cadastro') ?>";
    }
   
    $(function(){
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    if(gerencia_in(array('GP')))
    {
        $config['button'][] = array('Nova', 'cadastro()');
    }
    else
    {
        $config['button'] = array();
    }

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter();
            echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt. Inicio Atividade:');
            echo filter_dropdown('cd_plano', 'Plano:', $planos);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br();
    echo aba_end();

    $this->load->view('footer');
?>