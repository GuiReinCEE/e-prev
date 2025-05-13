<?php
set_title('Grupos de Indicadores');
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

	$.post( '<?php echo base_url() . index_page(); ?>/gestao/indicador_grupo/listar',{    },function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString',null
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
	ob_resul.sort(0, false);
}

function novo()
{
	location.href='<?php echo site_url("gestao/indicador_grupo/detalhe/0"); ?>';
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	 $("#filter_bar").hide(); $("#exibir_filtro_button").hide(); 
	
	filtrar();
}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
echo aba_start( $abas );

$config['button'][]=array('Novo', 'novo()');
echo form_list_command_bar($config);
echo form_start_box_filter('filter_bar', 'Filtros');

echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end(''); 
$this->load->view('footer');
?>