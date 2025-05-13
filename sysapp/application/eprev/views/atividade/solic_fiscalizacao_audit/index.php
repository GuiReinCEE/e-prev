<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('atividade/solic_fiscalizacao_audit/listar') ?>",
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
			"DateBR",
			"CaseInsensitiveString", 
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateTimeBR",
			"DateBR",
			null,
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
		location.href = "<?= site_url('atividade/solic_fiscalizacao_audit/cadastro') ?>";
	}

	$(function() {
        filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Registro', 'novo();');

    $enviado = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );

	echo aba_start($abas);
	    echo form_list_command_bar(($fl_permissao ? $config : array()));
	    echo form_start_box_filter();
			echo filter_dropdown('cd_solic_fiscalizacao_audit_origem', 'Origem:', $origem);			    	
	    	echo filter_date_interval('dt_recebimento_ini', 'dt_recebimento_fim', 'Dt. Recebimento:');
			echo filter_dropdown_optgroup('cd_solic_fiscalizacao_audit_tipo', 'Tipo:', $tipo); 	
			echo filter_dropdown('cd_gerencia', 'Área Concolidadora:', $gerencia);			    	
			echo filter_dropdown('cd_gestao', 'Gestão:', $gestao);			    	
			echo filter_text('ds_documento', 'Documento:', '', 'style="width:400px;"');		
			echo filter_text('ds_teor', 'Teor:', '', 'style="width:400px;"');		    	
	    	echo filter_date_interval('dt_prazo_ini', 'dt_prazo_fim', 'Dt. Prazo:');
	    	echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio:');
	    	echo filter_date_interval('dt_atendimento_ini', 'dt_atendimento_fim', 'Dt. Atendimento:');
	    	echo filter_dropdown('fl_enviado', 'Enviado:', $enviado);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>