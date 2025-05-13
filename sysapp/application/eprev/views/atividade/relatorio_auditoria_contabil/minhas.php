<?php
set_title('Relatórios de Auditoria Contábil - Responder Itens');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('atividade/relatorio_auditoria_contabil/listar_minhas');?>',
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
            "Number",
            "CaseInsensitiveString",
            "DateTimeBR",
            "DateBR",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR",            
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
        ob_resul.sort(0, true);
    }
		
    $(function(){
        filtrar();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr[] = array('text' => 'Sim', 'value' => 'S');
$arr[] = array('text' => 'Não', 'value' => 'N');

echo aba_start( $abas );
    echo form_list_command_bar(array());
    echo form_start_box_filter(); 
        echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio :');
        echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt. Limite :');
        echo filter_date_interval('dt_resposta_ini', 'dt_resposta_fim', 'Dt. Resposta :');
        echo filter_dropdown('fl_resposta', 'Resposta :', $arr);
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br();
echo aba_end();
$this->load->view('footer');
?>