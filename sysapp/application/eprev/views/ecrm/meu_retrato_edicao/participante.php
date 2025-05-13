<?php
	set_title('Meu Retrato Edição - Participante');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/meu_retrato_edicao/participante_listar') ?>",
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
		    "RE",
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
		ob_resul.sort(0, false);
	}

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/cadastro/'.$row['cd_edicao']) ?>";
    }
	
    function ir_verificar()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/verificar/'.$row['cd_edicao']) ?>";
    }		
				
	$(function(){
		if($("#qt_pagina").val() == "")
		{
			$("#qt_pagina").val(5000);
		}
		
		if($("#nr_pagina").val() == "")
		{
			$("#nr_pagina").val(1);
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_participante', 'Participante', TRUE, 'location.reload();');
	
	if(gerencia_in(array('GTI')))
	{
		$abas[] = array('aba_verificar', 'Verificar', FALSE, 'ir_verificar();');
	}		

	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
			echo form_default_hidden('cd_edicao', '', $row['cd_edicao']);
			echo filter_integer('qt_pagina', 'Qt por Página:');
			echo filter_integer('nr_pagina', 'Página:');
			echo filter_integer('cd_registro_empregado', 'RE:');
			echo filter_dropdown('desligado', 'Desligado:', array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')), array('N'));
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>