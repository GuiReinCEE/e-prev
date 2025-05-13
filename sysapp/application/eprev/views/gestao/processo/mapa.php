<?php
	if($fl_mostra_template == 'S')
	{
		set_title('Processos');
		$this->load->view('header');
	}
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('gestao/processo/mapa_listar') ?>",
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
			null,
			null,
			null,
			null,
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
		ob_resul.sort(0, false);
	}

	$(function(){
		//filtrar();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Processos', TRUE, 'location.reload();');

	$drop = array(
		array('value' => 'S', 'text' => 'Sim'), 
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);

	/*
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_gerencia_responsavel', 'Responsável:', $gerencia);
			//echo filter_dropdown('fl_versao_it', 'Novo Modelo de IT:', $drop);
		echo form_end_box_filter();
		*/
		/*
		echo form_start_box('legenda_box', 'Legenda');
			echo form_default_row('', '<span class="label label-success">Novo Modelo de IT</span>', '<span style="color: green; font-size: 120%"><b>Informações na cor VERDE</b></span>');
			echo form_default_row('', '<span class="label label-inverse">Modelo Antigo de IT</span>', '<span style="color: black; font-size: 120%;"><b>Informações na cor PRETA</b></span>');
		echo form_end_box('legenda_box');	
		*/
		echo '<center>';
		echo '<h1 style="color:red; font-size:120%;">Os processos estão disponíveis para consulta no S.A. (Interact) - PRODUÇÃO</h1>';
		echo '</center>';
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	if($fl_mostra_template == 'S')
	{
		$this->load->view('footer');
	}
?>