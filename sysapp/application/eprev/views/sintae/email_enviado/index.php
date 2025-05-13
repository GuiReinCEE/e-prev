<?php
$this->load->view('header');
?>
<script>
function filtrar()
{
	document.getElementById("current_page").value = 0;
	load();
}

function load()
{
	$.post( '<?php echo base_url() . index_page(); ?>/sintae/email_enviado/listar'
		,{
			current_page: document.getElementById("current_page").value
			, cd_registro_empregado: document.getElementById("cd_registro_empregado").value
			, inicio: document.getElementById("inicio").value
			, fim: document.getElementById("fim").value
			, assunto: document.getElementById("assunto").value
			, evento: document.getElementById("evento").value
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
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number"]);
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
</script>

	<div class="aba_conteudo">

		<?php
		echo form_list_command_bar();

		echo form_start_box_filter();
		echo form_default_date_interval("inicio", "fim", "Data:");
		echo form_default_text("cd_registro_empregado", "Registro do empregado");
		echo form_default_text("assunto", "Assunto");
		$eventos_dd[0] = array( 'value'=>'', 'text'=>'Todos para empresa SINTAE' );
		$eventos_dd[1] = array( 'value'=>'47', 'text'=>'Boas Vindas' );
		$eventos_dd[2] = array( 'value'=>'38', 'text'=>'Pré-cadastro' );
		$eventos_dd[3] = array( 'value'=>'39', 'text'=>'Email de contribuição' );
		echo form_default_dropdown("evento", "Evento", $eventos_dd);
		echo form_end_box_filter();
		?>

		<div id="result_div"></div>
		<br />
		<!-- BARRA DE COMANDOS -->

	</div>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer_interna');
?>