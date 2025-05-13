<?php
set_title('Reunião CCI - Responder');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
		
    $.post('<?php echo site_url('gestao/reuniao_cci/listar_minhas');?>',
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
		'CaseInsensitiveString',
        'CaseInsensitiveString',
		'DateBR',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
        'CaseInsensitiveString',
		'DateTimeBR',
        'CaseInsensitiveString',
        'DateTimeBR',
        'DateTimeBR'
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
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_status[] = array('value' => 'A', 'text' => 'Aprovado');
$arr_status[] = array('value' => 'D', 'text' => 'Desaprovado');

echo aba_start( $abas );
    echo (form_list_command_bar(array()));
    echo form_start_box_filter();
		echo filter_integer_ano('nr_numero', 'nr_ano', 'Número / Ano :');
		echo filter_dropdown('fl_status', 'Status :', $arr_status);
        echo filter_dropdown('cd_reuniao_cci_tipo', 'Tipo :', $arr_tipo);
        echo filter_date_interval('dt_ini', 'dt_fim', 'Data :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>