<?php
set_title('Cronograma - Concluídas Fora do Prazo');
$this->load->view('header');
?>

<script>

	function ir_lista()
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma"); ?>';
	}
	
	function ir_cadastro(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/cadastro"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_cronograma(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/cronograma"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_acompanhamento(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/acompanhamento"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function ir_quadro_resumo(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/quadro_resumo"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url("/atividade/atividade_cronograma/lista_concluidas_fora")?>',
		{
			cd_atividade_cronograma : <?php echo $cd_atividade_cronograma; ?>,
			cd_gerencia             : $('#cd_gerencia').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString"
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
	})
	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, "ir_cronograma('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Acompanhamento', FALSE, "ir_acompanhamento('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Quadro Resumo', FALSE, "ir_quadro_resumo('".$cd_atividade_cronograma."');");
	$abas[] = array('aba_cronograma', 'Concluídas Fora', TRUE, "location.reload();");
	
	echo aba_start( $abas );
		echo form_list_command_bar(array());	
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_dropdown('cd_gerencia', 'Gerência:', $arr_gerencias);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>