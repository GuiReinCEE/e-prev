<?php
	set_title('Regulamento de Plano - Minhas Atividades');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('atividade/regulamento_alteracao_atividade/listar_minhas') ?>",
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
	    	"CaseInsensitiveString",
	    	"Date",
	    	"Date",
	    	"CaseInsensitiveString",
	    	null
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
		
		ob_resul.sort(0, false);
	}

	$(function (){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload()');

	echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
        	echo filter_dropdown('fl_respondido', 'Respondido:', $drop);
        	echo filter_dropdown('cd_regulamento_alteracao_atividade_tipo', 'Pertinente:', $drop_tipo);
        	echo filter_dropdown('fl_implementado', 'Implementado:', $drop);
        	echo filter_date_interval('dt_prevista_ini', 'dt_prevista_fim', 'Dt. Previsto:');
        	echo filter_date_interval('dt_implementa_ini', 'dt_implementa_fim', 'Dt. Implementação:');
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();
	echo aba_end();
	echo br();
	$this->load->view('footer');
?>