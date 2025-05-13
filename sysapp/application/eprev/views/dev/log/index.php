<?php
set_title('{TITULO}');
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

	$.post( '<?php echo base_url() . index_page(); ?>/dev/log/listar'
		,{
			tipo: $('#tipo').val()
			,local: $('#local').val()
			,descricao: $('#descricao').val()
			,data_inicio: $('#data_inicio').val()
			,data_fim: $('#data_fim').val()
			,limite: $('#limite').val()

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
		'CaseInsensitiveString', 'CaseInsensitiveString', 'CaseInsensitiveString', 'DateTimeBR'
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
	ob_resul.sort(3, true);
}

</script>

	<div class="aba_conteudo">

		<?php
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_text('tipo', 'Tipo');
		echo form_default_text('local', 'Local');
		echo form_default_text('descricao', 'Descrição');
		echo form_default_date_interval('data_inicio', 'data_fim', 'Data');
		echo form_default_text('limite', 'Limite', 10);

		echo form_end_box_filter();
		?>

		<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
		<br />

	</div>

	<script type="text/javascript">
		//filtrar();
	</script>

<?php
$this->load->view('footer');
?>