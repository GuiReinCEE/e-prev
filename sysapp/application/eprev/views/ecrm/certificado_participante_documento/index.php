<?php
set_title('Certificados Participantes - Documentos');
$this->load->view('header');
?>
<script type="text/javascript">
$(function(){
   filtrar(); 
})

function filtrar()
{
    $('#result_div').html("<?php echo loader_html(); ?>");

    $.post( '<?php echo base_url() . index_page(); ?>/ecrm/certificado_participante_documento/listar',
    {
        cd_empresa : $("#cd_empresa").val()
    },
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
        'Number',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
        'CaseInsensitiveString',
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
    ob_resul.sort(0, false);
}

function novo()
{
    location.href='<?php echo site_url("ecrm/certificado_participante_documento/cadastro/"); ?>';
}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Incluir Documento', 'novo()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
        echo filter_dropdown('cd_empresa', 'Empresa:', $arr_patrocinadoras);
    echo form_end_box_filter();
echo aba_end();
?>

<div id="result_div"></div>
<br />

<?php $this->load->view('footer'); ?>.