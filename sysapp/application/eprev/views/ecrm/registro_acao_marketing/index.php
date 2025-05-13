<?php
set_title('Registro de Ações MKT - Lista');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?= loader_html() ?>");
	
    $.post("<?= site_url("ecrm/registro_acao_marketing/listar") ?>",
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
		'DateBR',
		'CaseInsensitiveString',
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
    ob_resul.sort(2, true);
}

function novo()
{
    location.href='<?= site_url("ecrm/registro_acao_marketing/cadastro") ?>';
}

$(function(){
    $("#dt_referencia_ini_dt_referencia_fim_shortcut").val("currentYear");
    $("#dt_referencia_ini_dt_referencia_fim_shortcut").change();

    filtrar();
})
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova Ação', 'novo()');

echo aba_start( $abas );
    echo (form_list_command_bar($config));
    echo form_start_box_filter();
        echo filter_text('ds_registro_acao_marketing', 'Descrição :', "", 'style="width:300px;"');
		echo filter_date_interval('dt_referencia_ini', 'dt_referencia_fim', 'Dt Referência :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer'); 