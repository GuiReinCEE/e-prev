<?php
set_title('Reclama��o - An�lise');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post('<?php echo site_url('ecrm/reclamacao_analise/listar');?>',
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
		'CaseInsensitiveString',
		'Number',
		'CaseInsensitiveString',
		'DateTimeBR',
		'DateBR',
		'DateBR',
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

function novo()
{
    location.href='<?php echo site_url("ecrm/reclamacao_analise/cadastro"); ?>';
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova An�lise', 'novo()');

$arr_yes_or_no[] = array('value' => 'S', 'text' => 'Sim');
$arr_yes_or_no[] = array('value' => 'N', 'text' => 'N�o');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/N�mero :');
		echo filter_dropdown('cd_reclamacao_analise_classifica', 'Classifica��o :', $arr_classificacao);
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio :');
		echo filter_dropdown('cd_usuario_responsavel', 'Respons�vel :', $arr_responsavel);
		echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt. Limite :');
		echo filter_date_interval('dt_prorrogacao_ini', 'dt_prorrogacao_fim', 'Dt. Prorroga��o :');
		echo filter_date_interval('dt_retorno_ini', 'dt_retorno_fim', 'Dt. Parecer da Ger�ncia :');
		echo filter_dropdown('fl_retornado', 'Retornado :', $arr_yes_or_no);
		echo filter_dropdown('fl_atrasado', 'Atrasado :', $arr_yes_or_no);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>