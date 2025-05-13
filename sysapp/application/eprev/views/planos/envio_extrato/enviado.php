<?php
	set_title('Envio Extrato');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('planos/envio_extrato/enviado_listar') ?>",
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
		    "Number",
		    "Number",
		    "Number",
		    "DateTime",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "Number",
		    "DateTimeBR",    
		    "Number"
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

	function ir_liberado()
	{
		location.href = "<?= site_url('planos/envio_extrato') ?>";
	}

	$(function(){
		$("#dt_gerado_ini_dt_gerado_fim_shortcut").val("currentMonth");
		$("#dt_gerado_ini_dt_gerado_fim_shortcut").change();
		filtrar();
	})
</script>
<?php  
	
	$abas[] = array('aba_liberados', 'Liberados', FALSE, 'ir_liberado();');
	$abas[] = array('aba_enviados', 'Enviados', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
			echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');
			echo filter_date_interval('dt_gerado_ini', 'dt_gerado_fim', 'Dt. Gerado:');
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio:');
			echo filter_integer('nro_extrato', 'Nr. Extrato:', '');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>