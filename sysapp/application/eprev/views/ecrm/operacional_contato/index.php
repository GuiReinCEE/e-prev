<?php
set_title('ECRM Operac - Contatos');
$this->load->view('header');
?>
<script>
function filtrar()
{
	load();
}

function load()
{
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/operacional_contato/listar'
		,{
			ano: $('#ano').val()

		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number','CaseInsensitiveString','DateTimeBR','CaseInsensitiveString','DateBR'
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

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');
// echo form_default_text('ano', 'Ano', date('Y'));
echo form_default_dropdown( 'ano', 'Ano', $anos, date('Y') );
echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php echo aba_end(''); ?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>