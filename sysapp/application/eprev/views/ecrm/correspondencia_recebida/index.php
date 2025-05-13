<?php
set_title('Protocolo Correspondência Recebida');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post( '<?php echo site_url('/ecrm/correspondencia_recebida/listar');?>',
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
		'CaseInsensitiveString',
		'Number',
		'Number',
		'Number',
		'',
		'DateTimeBR',
		'DateTimeBR',
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

function novo()
{
    location.href='<?php echo site_url("ecrm/correspondencia_recebida/cadastro/"); ?>';
}

function ir_relatorio()
{
    location.href='<?php echo site_url("ecrm/correspondencia_recebida/relatorio/"); ?>';
}

$(function(){
	$('#dt_inclusao_ini_dt_inclusao_fim_shortcut').val('last30days');
	$('#dt_inclusao_ini_dt_inclusao_fim_shortcut').change();
	
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Relatório', false, 'ir_relatorio();');

$arr_status[] = array('text' => 'Aguardando Envio',       'value' => 'AE');
$arr_status[] = array('text' => 'Aguardando Recebimento', 'value' => 'AR');
$arr_status[] = array('text' => 'Recebido',               'value' => 'RE');

$config['button'][]=array('Novo Protocolo', 'novo()');

echo aba_start( $abas );
    echo ((gerencia_in(array('GFC'))) ? form_list_command_bar($config) : form_list_command_bar());
    echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/Número:');
		echo filter_dropdown('cd_gerencia_destino', 'Gerência Destino:', $arr_gerencia);
		echo filter_dropdown('cd_correspondencia_recebida_grupo', 'Grupo Destino:', $arr_grupo);
		echo filter_dropdown('fl_status', 'Status:', $arr_status);
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro:');
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio:');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>