<?php
set_title('Sistema de Perguntas e Respostas');
$this->load->view('header');
?>
<script>
    function nova_pergunta()
    {
        location.href = "<?= site_url('cadastro/pergunta_resposta/cadastro') ?>";
    }

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('cadastro/pergunta_resposta/listar'); ?>",
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
		    "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
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

    $abas[] = array( 'aba_lista', 'Lista', TRUE, 'location.reload();' );

    $config['button'][] = array('Nova Pergunta', 'nova_pergunta();');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', FALSE);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>