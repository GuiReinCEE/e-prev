<?php
set_title('Atendimentos');
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

	$.post( '<?php echo base_url() . index_page(); ?>/ecrm/atendimento_lista/listar_atendente'
		,{
			dt_inicio: $('#dt_inicio').val()
			,dt_fim: $('#dt_fim').val()
			,tipo_atendimento: $('#tipo_atendimento').val()

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
		'CaseInsensitiveString','Number'
	]);
	ob_resul.onsort = function()
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

function mudar_obs(v)
{
	$('#obs').val(v);
	filtrar();
}

function ir_location(local)
{
    switch(local)
    {
    case 1:
      location.href='<?php echo site_url("ecrm/atendimento_lista/atendente"); ?>';
      break;
    case 2:
      location.href='<?php echo site_url("ecrm/atendimento_lista/data"); ?>';
      break;
    case 3:
      location.href='<?php echo site_url("ecrm/atendimento_lista/tipo"); ?>';
      break;
    case 4:
      location.href='<?php echo site_url("ecrm/atendimento_lista/programa"); ?>';
      break;
    case 5:
      location.href='<?php echo site_url("ecrm/atendimento_lista/index"); ?>';
      break;
    }
}

</script>

<?php
$abas[] = array('aba_lista', 'Atendente', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Data', FALSE, 'ir_location(2);');
$abas[] = array('aba_lista', 'Tipo', FALSE, 'ir_location(3);');
$abas[] = array('aba_lista', 'Programa', FALSE, 'ir_location(4);');
$abas[] = array('aba_lista', 'Todos', FALSE, 'ir_location(5);');

echo aba_start( $abas );

echo form_list_command_bar();
echo form_start_box_filter('filter_bar', 'Filtros');

	echo filter_date_interval('dt_inicio', 'dt_fim', 'Data:', calcular_data('', '3 days'), date('d/m/Y'), FALSE);
	echo filter_dropdown('tipo_atendimento', 'Tipo:', $tipo_dd);

echo form_end_box_filter();
?>

<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />

<?php
echo aba_end('');
?>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer');
?>