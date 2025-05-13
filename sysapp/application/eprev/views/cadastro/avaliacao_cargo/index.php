<?php
set_title('Avaliação - Cadastro de Cargo');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('/cadastro/avaliacao_cargo/listar');?>',
	{
		nome_cargo : $('#nome_cargo').val(),
		cd_familia : $('#cd_familia').val()
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
	ob_resul.sort(2, false);
}

function novo()
{
	location.href='<?php echo site_url("cadastro/avaliacao_cargo/cadastro"); ?>';
}
$(function (){
	filtrar();
});

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('nome_cargo', 'Cargo:');
		echo filter_dropdown('cd_familia', 'Família:', $arr_familia);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>