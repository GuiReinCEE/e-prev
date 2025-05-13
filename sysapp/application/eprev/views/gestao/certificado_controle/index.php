<?php
	set_title('Controle de Certificados');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('gestao/certificado_controle/listar') ?>",
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
		    "DateTimeBR",
			"CaseInsensitiveString",
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"CaseInsensitiveString",
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
		location.href = "<?= site_url('gestao/certificado_controle/cadastro') ?>";
	}
	
	function excluir(cd_certificado_controle)
	{	
		var confirmacao = 'Deseja excluir o certificado?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/certificado_controle/excluir') ?>/" + cd_certificado_controle;
		}
	}

	$(function(){
		if($("#fl_posse").val() == "")
		{	
			$("#fl_posse").val("S");
		}

		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Novo Controle', 'novo();');

	$drop = array(
		array('value' => 'N', 'text' => 'Não'), 
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter(); 
			echo filter_cpf('cpf', 'CPF:');
			echo filter_text('nome', 'Nome:', '', 'style="width:300px;"');
			echo filter_dropdown('cd_certificado_controle_tipo', 'Tipo Certificado:', $tipo_certificao);
			echo filter_dropdown('cd_certificado_controle_cargo', 'Cargo:', $cargo);
			echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão:');
			echo filter_date_interval('dt_certificao_ini', 'dt_certificao_fim', 'Dt. Certificado:');
			echo filter_date_interval('dt_expira_certificado_ini', 'dt_expira_certificado_fim', 'Dt. Termino Certificado:');
			echo filter_dropdown('fl_certificado', 'Certificado:', $drop);
			echo filter_dropdown('fl_recertificado', 'Recertificado:', $drop);
			echo filter_dropdown('fl_posse', 'Em posse:', $drop);
			echo filter_dropdown('fl_pontuacao', 'Pontuação:', $drop);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>