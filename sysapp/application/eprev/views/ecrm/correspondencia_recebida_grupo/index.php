<?php
set_title('Protocolo Correspondência Recebida - Grupo');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post( '<?php echo site_url('/ecrm/correspondencia_recebida_grupo/listar');?>',
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
    ob_resul.sort(0, false);
}

function novo()
{
    location.href='<?php echo site_url("ecrm/correspondencia_recebida_grupo/cadastro/"); ?>';
}

function excluir(cd_correspondencia_recebida_grupo)
{
	if(confirm("Deseja excluir o grupo?"))
	{
		location.href='<?php echo site_url("ecrm/correspondencia_recebida_grupo/excluir"); ?>/'+cd_correspondencia_recebida_grupo;
	}
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo grupo', 'novo()');

echo aba_start( $abas );
    echo ((gerencia_in(array('GAD'))) ? form_list_command_bar($config) : form_list_command_bar());
    echo form_start_box_filter();
		echo filter_dropdown('cd_correspondencia_recebida_grupo', 'Grupo Destino:', $arr_grupo);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>