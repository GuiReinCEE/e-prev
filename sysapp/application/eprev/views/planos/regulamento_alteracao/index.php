<?php
	set_title('Alteração de Regulamento');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
        $.post("<?= site_url('planos/regulamento_alteracao/listar') ?>",
        $("#filter_bar_form").serialize(),
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        });	
	}

    $(function(){
        filtrar();
    });

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
            "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateBR",
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
		ob_resul.sort(0, true);
	}
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
        
    echo aba_start($abas);
        echo form_list_command_bar(array());
        echo form_start_box_filter('filter_bar', 'Filtros', true);
        echo form_end_box_filter();	
        echo '<div id="result_div"></div>';
        echo br();
    echo aba_end('');

    $this->load->view('footer');
?>