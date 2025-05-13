<?php
	set_title('Área Corporativa - SENGE Previdência');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		if($("#cd_usuario").val() != "")
		{		
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('planos/area_corporativa_senge/listar') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});	
		}
		else
		{
			$("#result_div").html('<br/><span class="label label-important">Informe um Usuário para Filtrar.</span>')
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			'CaseInsensitiveString'
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

	$(function(){
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter(); 
			echo filter_dropdown('cd_usuario', 'Usuário:', $usuario);
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão:');			    	
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>