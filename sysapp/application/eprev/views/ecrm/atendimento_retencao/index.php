<?php
	set_title('Atendimento Retenção');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/atendimento_retencao/listar') ?>",
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
			"Number",
		    "CaseInsensitiveString",
			"RE",
		    "CaseInsensitiveString",
		    "DateTimeBR",
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
		location.href = "<?= site_url('ecrm/atendimento_retencao/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Retenção', 'novo();');
	
	$conf = array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'nome_participante');

	$retido = array(
		array('text' => 'Não', 'value' => 'N'),
		array('text' => 'Sim', 'value' => 'S')
	);

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter();
			echo filter_participante($conf, 'Participante:', array(), FALSE, TRUE, TRUE); 	
			echo filter_date_interval('dt_ini', 'dt_fim', 'Dt. Inclusão:');
			echo filter_dropdown('cd_usuario', 'Usuário:', $usuario);
			echo filter_dropdown('fl_retido', 'Retido:', $retido);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>