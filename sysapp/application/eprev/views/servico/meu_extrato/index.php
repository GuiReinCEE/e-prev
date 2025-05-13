<?php
set_title('Meu Extrato');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
	
	$.post( '<?php echo site_url('/servico/meu_extrato/listar'); ?>',
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
		'DateBR'
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

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>