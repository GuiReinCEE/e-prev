<?php
    set_title('Contribuição Patrocinadora');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");

        $.post("<?= site_url('planos/contribuicao_patrocinadora/listar') ?>",
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
            "DateTimeBR",
            "Number",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
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
					
    $(function(){
        if($("#dt_solicitacao_ini").val() == "" || $("#dt_solicitacao_fim").val() == "")
        {
            $("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").val("currentMonth");
            $("#dt_solicitacao_ini_dt_solicitacao_fim_shortcut").change();
        }
        
        filtrar();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_list_command_bar();
        echo form_start_box_filter(); 
            echo filter_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'), 'Participante:', array(), TRUE, FALSE);
            echo filter_date_interval('dt_solicitacao_ini', 'dt_solicitacao_fim', 'Dt. Solicitação:');            
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br();
    echo aba_end();

    $this->load->view('footer');
?>