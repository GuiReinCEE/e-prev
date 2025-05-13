<?php
set_title('Competências Específicas');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('/cadastro/avaliacao_competencia_especifica/listar');?>',
	{
		nome : $('#nome').val()
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
	ob_resul.sort(1, false);
}

function novo()
{
	location.href='<?php echo site_url("cadastro/avaliacao_competencia_especifica/cadastro"); ?>';
}

function ir_escala()
{
	location.href='<?php echo site_url("cadastro/avaliacao_competencia_especifica/escala"); ?>';
}

$(function(){
	filtrar();
});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Escala', FALSE, 'ir_escala();');

$config['button'][] = array('Novo', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('nome', 'Nome :');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 
$this->load->view('footer');
?>