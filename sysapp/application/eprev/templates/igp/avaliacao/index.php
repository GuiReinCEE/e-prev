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

	$.post( '<?php echo base_url() . index_page(); ?>/igp/avaliacao/listar', {}, function(data){ $("#result_div").html(data);configure_result_table(); } );
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',null,null,null,null,null,null
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
	location.href='<?php echo site_url("igp/avaliacao/detalhe/0"); ?>';
}

$(document).ready( rodar_ao_iniciar );

function rodar_ao_iniciar()
{
	filtrar();
}

function gerar_graficos()
{
	if( confirm('Atualizar Indicadores ?') )
	{
		url = '<?php echo base_url() . index_page(); ?>/igp/avaliacao/criar_indicador';

		$.post( url, {}, function(data){ $('#output_tela').html(data); } );
	}
}

function exibir_grafico()
{
	url = '<?php echo site_url("indicador/apresentacao/detalhe/".enum_indicador::AVALIACAO); ?>';
	window.open(url);
}

function configurar_grafico()
{
	url = '<?php echo site_url("indicador/tabela/index/".enum_indicador::AVALIACAO); ?>';
	location.href = url;
}

function fechar_periodo()
{
	if( $('#contador_input').val()!='12' )
	{
		alert( "Falta algum m�s." );
	}
	else if( $('#mes_input').val()!='12' )
	{
		alert( "�ltimo m�s deve ser dezembro." );
	}
	else if( confirm('Fechar o per�odo?') )
	{
		url = '<?php echo site_url("igp/avaliacao/criar_indicador/"); ?>';
		$.post(url,{},function(data){

			$('#output_tela').html( "Indicadores atualizados com sucesso, aguarde enquanto o per�odo � fechado ..." );

			url = '<?php echo site_url("igp/avaliacao/fechar_periodo")?>';
			location.href=url;
		});
	}
}

function manutencao()
{
    location.href='<?php echo site_url("indicador/manutencao/"); ?>';
}

</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array( 'aba_lista', 'Lan�amento', true, 'location.reload();' );
echo aba_start( $abas );

echo "<div id='output_tela'></div>";

$config['button'][]=array('Informar valores', 'novo()');
$config['button'][]=array('Atualizar apresenta��o', 'gerar_graficos()');
$config['button'][]=array('Fechar Per�odo', 'fechar_periodo()');
echo form_list_command_bar($config);
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