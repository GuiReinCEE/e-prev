<?php
	set_title('Controle de Documento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url("gestao/controle_documento_controladoria/listar") ?>",
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
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateTimeBR"
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
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_controle_documento_controladoria_tipo', 'Tipo Documento:', $doc_tipo);
			echo filter_dropdown('fl_envio', 'Enviado: ', array(array('value' => 'S', 'text' => 'Sim'), array('value' => 'N', 'text' => 'Não')));
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Atualização:');
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio:');
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 
	$this->load->view('footer');
?>