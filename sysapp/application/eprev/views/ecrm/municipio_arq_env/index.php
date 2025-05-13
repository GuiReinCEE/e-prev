<?php
	set_title('Família Munícipios - Arquivos Recebidos');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/municipio_arq_env/listar') ?>",
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

	$(function(){
		if($("#tp_status").val() == '')
		{
			$("#tp_status").val('E');
		}

		filtrar();
	});

	function aceitar(cd_municipio_arq_env)
	{
		var confirmacao = 
		 	'Deseja confirmar o Documento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('ecrm/municipio_arq_env/aceitar') ?>/" + cd_municipio_arq_env;
		}
}
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
            echo filter_date_interval('dt_encaminhamento_ini', 'dt_encaminhamento_fim', 'Dt. Encaminhamento:');
            echo filter_dropdown('cd_empresa', 'Empresa:', $empresa);
			echo filter_dropdown('tp_status', 'Status:', $status);
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>