<?php
set_title('Aviso Gestão - Controle');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('gestao/gestao_aviso/listar_controle_pendencia') ?>",
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
		    "CaseInsensitiveString",
		    null,
			"DateBR",
			"DateTimeBR",
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
		ob_resul.sort(3, true);
	}
	
	$(function(){
		if($("#dt_referencia_ini").val() == "")
	    {
	    	$("#dt_referencia_ini").val("01/01/<?= date('Y') ?>");
	    	$("#dt_referencia_fim").val("<?= date('d/m/Y') ?>");
	    }

		filtrar();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start($abas);

		echo form_list_command_bar(array());
		
		echo form_start_box_filter('filter_bar', 'Filtros');
			echo filter_date_interval('dt_referencia_ini', 'dt_referencia_fim', 'Dt. Prazo:');
	        echo filter_date_interval('dt_verificacao_ini', 'dt_verificacao_fim', 'Dt. Verificado:');
	        echo form_default_dropdown('fl_verificado', 'Verificado:', array(array('value' => 'S', 'text' => 'Sim'), array('value' => 'N', 'text' => 'Não')));
		echo form_end_box_filter();

		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

$this->load->view('footer');
?>