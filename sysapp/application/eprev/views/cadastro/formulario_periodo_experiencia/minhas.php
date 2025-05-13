	<?php
	set_title('Minhas solicitações');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('cadastro/formulario_periodo_experiencia/minhas_listar') ?>",
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
	        "DateTimeBR", 
	        "CaseInsensitiveString", 
	        "DateTimeBR", 
	        "CaseInsensitiveString", 
	        "CaseInsensitiveString",
	        "DateTimeBR", 
	        false
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
    $abas[] = array('aba_soliticaoes', 'Solicitações', TRUE, 'location_reload();');

	$respondido = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'Não')
	);

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_usuario_avaliado', 'Avaliado:', $avaliado);
			echo filter_dropdown('fl_resposta', 'Respondido:', $respondido);
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Solicitação:');
			echo filter_date_interval('dt_limite_ini', 'dt_limite_fim', 'Dt. Limite:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>