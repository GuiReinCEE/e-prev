<?php
	set_title('Informativo do Cenário Legal');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('ecrm/informativo_cenario_legal/consulta_normativo_listar') ?>",
		$("#filter_bar_form").serialize(),
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
			null,
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
		if($("#dt_ini").val() == "" || $("#dt_fim").val() == "")
		{
			$("#dt_ini_dt_fim_shortcut").val("currentYear");
			$("#dt_ini_dt_fim_shortcut").change();
		}

		filtrar();
	});

</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_integer('cd_edicao', 'Edição:', $cd_edicao);
			echo filter_text('nome', 'Título:', '', 'style="width:320px;"');
			echo filter_date_interval('dt_ini', 'dt_fim', 'Período:');
			echo filter_text('conteudo', 'Normativo:', '', 'style="width:320px;"');
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

	$this->load->view('footer');
?>