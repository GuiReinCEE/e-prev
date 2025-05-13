<?php
set_title('Meus Relatórios');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
	$.post('<?php echo site_url('/servico/relatorio/listar'); ?>',
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
		'CaseInsensitiveString',
		'DateBR',
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
	ob_resul.sort(0, false);
}

function novo()
{
	location.href='<?php echo site_url("servico/relatorio/cadastro"); ?>';
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
if (gerencia_in(array('GI')))
{
	$config['button'][] = array('Novo', 'novo()');
}
$config['filter']   = false;
echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>