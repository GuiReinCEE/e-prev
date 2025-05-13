<?php
set_title('Atualiza Intranet - Subitens');
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

	$.post( '<?php echo site_url('ecrm/intranet/listar_subitem'); ?>',
	{
		cd_gerencia     : '<?php echo $cd_gerencia; ?>',
		cd_intranet_pai : '<?php echo $cd_gerencia; ?>'
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
	ob_resul.sort(1, false);
}

function novo()
{
	location.href='<?php echo site_url('ecrm/intranet/cadastro/'.$cd_gerencia); ?>';
}

function ir_lista()
{
	location.href='<?php echo site_url("ecrm/intranet/index/".$cd_gerencia); ?>';
}

$(function(){
	filtrar();
})
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Subitens', TRUE, 'location.reload();');

	$config['button'][] = array('Novo', 'novo()');
	$config['filter'] = false;

	echo aba_start( $abas );
		echo form_list_command_bar($config);	
		echo '<div id="result_div"></div>';
	echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>