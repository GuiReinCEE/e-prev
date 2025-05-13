<?php
set_title('Área Corporativa - Relatório Agendamentos');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url("/planos/sinprors_relatorio_agenda/listar"); ?>',
        {
            nr_ano : $('#nr_ano').val()
        },
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
            'CaseInsensitiveString',
            'Number',
            'Number',
            'Number',
            'Number'
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
</script>

<?php
$abas[] = array('aba_lista', 'Contato', TRUE, 'location.reload();');
echo aba_start($abas);

    echo form_list_command_bar();
    
    echo form_start_box_filter();
        echo filter_integer('nr_ano', 'Ano :', date("Y"));
    echo form_end_box_filter();

?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end('');
?>

<script type="text/javascript">
    filtrar();
</script>

<?php
$this->load->view('footer');
?>