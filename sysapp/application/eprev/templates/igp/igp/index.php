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

	$.post( '<?php echo base_url().index_page(); ?>/igp/igp/listar',{}, function(data){ $("#result_div").html(data); configure_result_table(); } );
}

function atualizar_indicador()
{
	if( confirm('Atualizar Indicadores ?') )
	{
		url = '<?php echo base_url() . index_page(); ?>/igp/igp/atualizar_indicador';

		$.post( url, {}, function(data){ $('#output_tela').html(data); } );
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number', 'Total'
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
	//ob_resul.sort(0, true);
	
	var ob_resul = new SortableTable(document.getElementById("table-2"),
	[
		'Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number', 'Total'
	]);
	//ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	//ob_resul.sort(0, true);

	var ob_resul = new SortableTable(document.getElementById("table-3"),
	[
		'Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number', 'Total'
	]);
	//ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	//ob_resul.sort(0, true);

	var ob_resul = new SortableTable(document.getElementById("table-4"),
	[
		'Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number','Number', 'Total'
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
	//ob_resul.sort(0, true);
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	 $("#filter_bar").hide(); $("#exibir_filtro_button").hide(); 
	
	filtrar();
}

function exibir_grafico()
{
	url = '<?php echo site_url("/indicador/apresentacao/detalhe/".enum_indicador::IGP); ?>';
	window.open(url);
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

echo "<div id='output_tela'></div>";
$config['button'][]=array('Atualizar apresentação', 'atualizar_indicador()');
echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros', false);
echo form_default_row('','','Exibindo todos os registros');
echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>