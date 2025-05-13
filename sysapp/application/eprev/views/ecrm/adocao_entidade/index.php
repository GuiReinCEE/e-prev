<?php
set_title('Adoção de Entidades');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post('<?php echo site_url('ecrm/adocao_entidade/listar');?>',
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
    location.href='<?php echo site_url("ecrm/adocao_entidade/cadastro/"); ?>';
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_tipo[] = array('value' => 'C', 'text' => 'Crianças');
$arr_tipo[] = array('value' => 'I', 'text' => 'Idosos');

$config['button'][] = array('Nova Entidade', 'novo()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo filter_text('ds_adocao_entidade', 'Nome da Entidade :', '', 'style="width:300px;"');
		echo filter_dropdown('cd_adocao_entidade_periodo', 'Período :', $arr_periodo);     
		echo filter_dropdown('fl_adocao_entidade_tipo', 'Tipo :', $arr_tipo);     
		echo filter_date_interval('dt_adocao_entidade_acompanhamento_ini', 'dt_adocao_entidade_acompanhamento_fim', 'Dt Acompanhamento :'); 
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>