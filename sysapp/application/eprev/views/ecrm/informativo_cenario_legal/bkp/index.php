<?php
set_title('Informativo do Cen�rio Legal');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('ecrm/informativo_cenario_legal/listar');?>',
	{
		nome      : $('#nome').val(),
		cd_edicao : $('#cd_edicao').val(),
		dt_ini    : $('#dt_ini').val(),
		dt_fim    : $('#dt_fim').val()

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
	ob_resul.sort(3, true);
}

function novo()
{
	location.href='<?php echo site_url('ecrm/informativo_cenario_legal/cadastro/')?>';
}

$(function(){
	filtrar();
});

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova Edi��o do Cen�rio Legal', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('nome', 'T�tulo:');
		echo filter_integer('cd_edicao', 'Edi��o:');
		echo filter_date_interval('dt_ini', 'dt_fim', 'Per�odo:');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end(); 
$this->load->view('footer');
?>