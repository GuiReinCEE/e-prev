<?php
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);

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

	$.post( '<?php echo base_url().index_page(); ?>/indicador_plugin/administrativo_doc_digitalizados_acum/listar',{},function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',null
	]);

	ob_resul.onsort = function()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for(var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};

	ob_resul.sort(0, false);
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	filtrar();
}

function manutencao()
{
    location.href='<?php echo site_url("indicador/manutencao/"); ?>';
}

</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array( 'aba_lista', 'Lançamento', true, 'location.reload();' );
echo aba_start( $abas );

echo "<div id='div-output'></div>";

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');
echo form_default_row( "", $ds_tabela_periodo, "" );
echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>
