<?php
	set_title('Fam�lia Mun�cipios - Usu�rios');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/municipio_usuario/listar') ?>",
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
		/*
			"Number",
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"Number",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    null
		    */
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
		ob_resul.sort(5, true);
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/municipio_usuario/cadastro') ?>";
	}

	$(function(){
		if($("#fl_interno").val() == '')
		{
			$("#fl_interno").val('N');
		}

		filtrar();
	});

</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova', 'novo();');

	$interno = array(
		array('value' => 'S', 'text' => 'Sim'),
		array('value' => 'N', 'text' => 'N�o'),
	);

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_empresa', 'Empresa:', $empresa);
			echo filter_dropdown('fl_interno', 'Interno:', $interno);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>