<?php 
set_title('Clipping Diário - Lista');
$this->load->view('header'); 
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url("ecrm/informativo/listar"); ?>', 
	$('#filter_bar_form').serialize(),
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
	location.href='<?php echo site_url("ecrm/informativo/cadastro"); ?>';
}

function ir_resumo()
{
	location.href='<?php echo site_url("ecrm/informativo/resumo"); ?>';
}

$(function(){
	filtrar();
});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Resumo', FALSE, 'ir_resumo();');

$config['button'][] = array('Novo Registro', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo filter_date_interval('dt_ini', 'dt_fim', 'Data :', calcular_data('','1 month'), date('d/m/Y'));
		echo filter_dropdown("id_noticia_editorial", "Editorial :", $arr_editorial); 
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end();
$this->load->view('footer');
?>
