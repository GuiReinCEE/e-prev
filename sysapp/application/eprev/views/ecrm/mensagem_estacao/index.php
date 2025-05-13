<?php
set_title('Mensagens nas Estações');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('/ecrm/mensagem_estacao/listar'); ?>',
	{
		dt_inicio_ini : $('#dt_inicio_ini').val(), 
		dt_inicio_fim : $('#dt_inicio_fim').val()
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
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'DateTimeBR',
		'DateTimeBR',
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
	ob_resul.sort(2, true);
}

function novo()
{
	location.href='<?php echo site_url("ecrm/mensagem_estacao/cadastro"); ?>';
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova mensagem', 'novo()');
$config['filter'] = false;

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter();
        echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt Início :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>