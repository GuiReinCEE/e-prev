<?php
	set_title('Solicitação Entrega Documento');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('ecrm/solic_entrega_documento/listar') ?>",
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
			"DateBR",
			null,
			null,
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"DateTimeBR",
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
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(1, true);
	}

	function novo()
	{
		location.href = "<?= site_url('ecrm/solic_entrega_documento/cadastro') ?>";
	}

	function receber(cd_solic_entrega_documento)
	{
		var confirmacao = 'Deseja confirmar o recebimento do documento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

         if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('ecrm/solic_entrega_documento/receber') ?>/"+ cd_solic_entrega_documento;
        }
	}

	$(function() {
        filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Nova Solicitação', 'novo();');

    $recebido = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );

    $prioridade = array(
        array('value' => 'U', 'text' => 'Urgente'),
        array('value' => 'M', 'text' => 'Moderada'),
        array('value' => 'B', 'text' => 'Baixa')
    );

	echo aba_start($abas);
	    echo form_list_command_bar($config);
	    echo form_start_box_filter();
	    	echo filter_dropdown('cd_solic_entrega_documento_tipo', 'Tipo de Documento:', $solic_entrega_documento);
	    	echo filter_dropdown('fl_prioridade', 'Prioridade:', $prioridade);
	    	echo filter_date_interval('dt_ini', 'dt_fim', 'Data:');
	    	echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Data Recebido:');
	    	echo filter_dropdown('fl_recebido', 'Recebido:', $recebido);
	    	echo filter_dropdown('fl_status', 'Status:', $status);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer'); 
?>