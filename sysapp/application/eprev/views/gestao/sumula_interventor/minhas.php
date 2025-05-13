<?php
set_title('Súmulas Interventor - Responder Itens');
$this->load->view('header');
?>
<script type="text/javascript">
function filtrar()
{
    load();
}

function load()
{
    document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

    $.post( '<?php echo base_url() . index_page(); ?>/gestao/sumula_interventor/minhas_listar',
    {
        fl_respondido : $('#fl_respondido').val(),
        dt_ini_envio  : $('#dt_ini_envio').val(),
        dt_fim_envio  : $('#dt_fim_envio').val(),
        dt_ini_resp   : $('#dt_ini_resp').val(),
        dt_fim_resp   : $('#dt_fim_resp').val(),
        nr_sumula_interventor     : $('#nr_sumula_interventor').val()
    },
    function(data)
    {
        document.getElementById("result_div").innerHTML = data;
        configure_result_table();
    });
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
        'CaseInsensitiveString',
        null,
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'DateTimeBR',
        'DateBR',
        'DateTimeBR',
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
    location.href='<?php echo site_url("gestao/sumula_interventor/cadastro/"); ?>';
}

$(function(){
    filtrar();
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_respondido[] = Array('value' => 'S', 'text' => 'Sim');
$arr_respondido[] = Array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
    echo form_list_command_bar(array());
    echo form_start_box_filter();
        echo filter_dropdown('fl_respondido', 'Respondido :', $arr_respondido);
        echo filter_date_interval('dt_ini_envio', 'dt_fim_envio', 'Dt Envio :');
        echo filter_date_interval('dt_ini_resp', 'dt_fim_resp', 'Dt Resposta :');
        echo filter_integer('nr_sumula_interventor', 'Súmula :');
    echo form_end_box_filter();
echo aba_end();
?>

<div id="result_div"></div>
<br />
<?php $this->load->view('footer'); ?>.