<?php
set_title('Plano Fiscal - Indicadores PGA - Diretoria Assinar');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $('#result_div').html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('gestao/plano_fiscal_indicador/listar_diretoria_assinar')?>', $('#filter_bar_form').serialize(),
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
			'DateTimeBR',
            'CaseInsensitiveString'
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

$arr_assinado[] = array('value' => 'N', 'text' => 'Não');
$arr_assinado[] = array('value' => 'S', 'text' => 'Sim');

echo aba_start( $abas );
    echo form_list_command_bar(array());
    echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_mes', 'Ano/Mês :');
		echo filter_dropdown('fl_assinado', 'Assinado :', $arr_assinado);
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);

echo aba_end();

$this->load->view('footer'); ?>.