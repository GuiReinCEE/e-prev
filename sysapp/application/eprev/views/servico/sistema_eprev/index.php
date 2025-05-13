<?php
	set_title('Sistema e-prev');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?= loader_html() ?>");

		$.post("<?= site_url('servico/sistema_eprev/listar') ?>",
		$("#filter_bar_form").serialize(),	
		function(data)
		{
			$('#result_div').html(data);
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
			"DateBR",
			"CaseInsensitiveString",
			null
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

	function novo()
	{
		location.href = "<?= site_url('servico/sistema_eprev/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Sistema', 'novo()');

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_text('ds_sistema', 'Sistema :', '', 'style="width:300px;"');
			echo filter_dropdown('cd_gerencia_responsavel', 'Gerência Responsável: ', $gerencia);
			echo filter_dropdown('cd_usuario_responsavel', 'Responsável:', $responsavel);
			echo filter_dropdown('cd_usuario_solicitante', 'Solicitante:', $solicitante);
			echo filter_dropdown('fl_publicado', 'Publicado:', array(array('text' => 'Sim', 'value' => 'S'), array('text' => 'Não' , 'value' => 'N')));
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 

	$this->load->view('footer');
?>