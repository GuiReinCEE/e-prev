<?php
	set_title('Sistema de Avalia��o - Cargo/�rea de Atua��o');
	$this->load->view('header');
?>
<script>
    function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_cargo_area_atuacao/listar') ?>",
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
        location.href = "<?= site_url('cadastro/rh_cargo_area_atuacao/cadastro') ?>";
    }

    $(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $config['button'][] = array('Novo Cargo/�rea de Atua��o', 'cadastro();');

    echo aba_start($abas);
        echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
        	echo filter_dropdown('cd_gerencia', 'Ger�ncia:', $gerencia);
        	echo filter_dropdown('cd_grupo_ocupacional', 'Grupo Ocupacional:', $grupo_ocupacional);
        	echo filter_dropdown('cd_cargo', 'Cargo:', $cargo);
        	echo filter_dropdown('cd_area_atuacao', '�rea de Atua��o', $area_atuacao);
        echo form_end_box_filter();
        echo '<div id="result_div"></div>';
		echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>