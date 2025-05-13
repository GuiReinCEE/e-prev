<?php
set_title('Registro Operacional');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('atividade/registro_operacional/listar'); ?>',
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

function novo()
{
	location.href = '<?php echo site_url('atividade/registro_operacional/cadastro'); ?>';
}

function excluir(cd_acompanhamento_registro_operacional)
{
	if(confirm('Deseja realmente excluir o registro operacional?\n\n'))
	{
		$.post('<?php echo site_url('atividade/registro_operacional/excluir'); ?>',
		{
			cd_acompanhamento_registro_operacional : cd_acompanhamento_registro_operacional
		},
		function(data)
		{
			filtrar();
		});
	}
}

function imprimir(cd_acompanhamento_registro_operacional)
{
	window.open('<?php echo site_url("atividade/registro_operacional/imprimir/"); ?>/'+cd_acompanhamento_registro_operacional);
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo registro operacional', 'novo()');
$config['filter']   = false;

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 

$this->load->view('footer');
?>