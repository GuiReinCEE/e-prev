<?php
set_title('Entidade - Movimento');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('atividade/entidade_movimento/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
		
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"MesAno",
			"DateTimeBR",
			"DateTimeBR",
			null
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
		ob_resul.sort(0, true);
	}
	
	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo filter_integer_ano('nr_ano', 'nr_numero', 'Ano/Número :');
		echo filter_mes_ano('nr_mes_ref', 'nr_ano_ref', 'Mês/Ano Ref :');
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Alteração :');
		echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Dt. Recebido :');
		echo filter_date_interval('dt_retorno_ini', 'dt_retorno_fim', 'Dt. Retorno :');
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');