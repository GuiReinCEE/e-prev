<?php
	set_title('Sistema de Avaliação - Matriz Quadro');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_matriz_quadro/listar') ?>",
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

    function cadastro()
    {
        location.href = "<?= site_url('cadastro/rh_matriz_quadro/cadastro') ?>";
    }

    function ir_matriz()
    {
    	location.href = "<?= site_url('cadastro/rh_matriz_quadro/matriz') ?>";
    }

    $(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_matriz', 'Matriz', FALSE, 'ir_matriz();');

    $config['button'][] = array('Nova Matriz Quadro', 'cadastro();');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
            echo filter_dropdown('cd_matriz_conceito', 'Conceito:', $conceito);
            echo filter_dropdown('cd_matriz_acao', 'Ação:', $acao);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>