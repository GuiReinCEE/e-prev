<?php
	set_title('Treinamento Colaborador - Replica');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('servico/avaliacao_treinamento_replica/listar') ?>",
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
		    "Numeric",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
            "DateTimeBR",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
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

    echo aba_start($abas);
        echo form_list_command_bar();
        echo form_start_box_filter(); 
            echo filter_integer('numero', 'Número:');
            echo filter_integer('ano', 'Ano:');
            echo filter_text('nome', 'Treinamento:', '', 'style="width:300px;"');
            echo filter_date_interval('dt_inicio_ini', 'dt_inicio_fim', 'Dt Inicio:');
            echo filter_date_interval('dt_final_ini', 'dt_final_fim', 'Dt Final:');
            echo filter_dropdown('cd_treinamento_colaborador_tipo', 'Tipo:', $tipo); 
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
        echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>