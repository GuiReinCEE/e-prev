<?php
set_title('Súmulas Conselho - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post( '<?php echo site_url('/gestao/sumula_conselho/consulta_listar');?>',
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
		'Number',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateBR',
		'DateBR', 
		'Number',
		'Number',
		'Number',
		'CaseInsensitiveString',
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
	filtrar();
})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_list_command_bar();
    echo form_start_box_filter();
        echo filter_integer('nr_sumula_conselho', 'Nº da súmula :');
        echo filter_text('descricao', 'Assunto :', '', "style='width:300px;'");
        echo filter_date_interval('dt_ini', 'dt_fim', 'Dt Súmula :');
        echo filter_date_interval('dt_div_ini', 'dt_div_fim', 'Dt Divulgação :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>