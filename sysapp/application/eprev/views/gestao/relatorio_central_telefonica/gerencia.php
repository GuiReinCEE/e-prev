<?php
set_title('Relatório Telefone - Geral');
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

	$.post( '<?php echo base_url() . index_page(); ?>/gestao/relatorio_central_telefonica/gerenciaResumo'
		,{
			dt_ini : $('#dt_ini').val(),
			dt_fim : $('#dt_fim').val()
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
		'Number',
		'Number',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'NumberFloatBR'
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
	ob_resul.sort(4, true);
}

function relValor()
{
	location.href='<?php echo site_url("gestao/relatorio_central_telefonica"); ?>';
}

function relDuracao()
{
	location.href='<?php echo site_url("gestao/relatorio_central_telefonica/duracao"); ?>';
}

function relQuantidade()
{
	location.href='<?php echo site_url("gestao/relatorio_central_telefonica/quantidade"); ?>';
}
</script>

<?php
	$abas[] = array('aba_lista', 'Valor', FALSE, 'relValor();');
	$abas[] = array('aba_lista', 'Duração', FALSE, 'relDuracao();');
	$abas[] = array('aba_lista', 'Quantidade', FALSE, 'relQuantidade();');
	$abas[] = array('aba_lista', 'Gerências', TRUE, 'location.reload();');
	echo aba_start( $abas );

	echo form_list_command_bar();

	echo form_start_box_filter('filter_bar', 'Filtros');
	echo form_default_date_interval('dt_ini', 'dt_fim', 'Período:',date("01/m/Y"),date('d/m/Y'));

	echo form_end_box_filter();
?>

<div id="result_div"><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
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