<?php
	set_title('Indicadores de Gestão do PGA – Avaliação da Diretoria Executiva');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/relatorio_avaliacao_pga/diretoria_listar') ?>",
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
			"CaseInsensitiveString",
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

	$arr_assinado[] = array('value' => 'N', 'text' => 'Não');
	$arr_assinado[] = array('value' => 'S', 'text' => 'Sim');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_integer('nr_ano', 'Ano:');
			echo filter_dropdown('nr_trimestre', 'Trimestre:', $trimestres);
			echo filter_dropdown('fl_assinado', 'Assinado :', $arr_assinado);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>