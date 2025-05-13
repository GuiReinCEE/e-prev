<?php
set_title('Cadastro Planos Certificado');
$this->load->view('header');
?>
<script>
function filtrar()
{
	if($('#cd_plano').val() != '')
	{	
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('ecrm/cadastro_plano_certificado/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	else
	{
		alert('Informe o plano.');
	}
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
		'Number',
		'CaseInsensitiveString',
		'DateTime',
		'DateTime'
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
    location.href='<?php echo site_url("ecrm/cadastro_plano_certificado/cadastro/"); ?>';
}

$(function(){
	if($('#cd_plano').val() != '')
	{
		filtrar();
	}
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_list_command_bar();
    echo form_start_box_filter();
		echo filter_dropdown('cd_plano', 'Plano: *', $arr_plano);
    echo form_end_box_filter();
	echo '<div id="result_div"><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>