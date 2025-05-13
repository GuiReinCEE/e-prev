<?php
	set_title('Pagamento Impressão');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if 	(
				(($('#cd_empresa').val() != "") && ($('#cd_registro_empregado').val() != "") && ($('#seq_dependencia').val() != ""))
				||
				(($('#dt_impressao_ini').val() != "") && ($('#dt_impressao_fim').val() != ""))
				||
				(($('#dt_vencimento_ini').val() != "") && ($('#dt_vencimento_fim').val() != ""))
			)
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/pagamento_impressao/listar',
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
			);
		}
		else
		{
			alert("Informe o Participante ou Período de Impressão ou Período de Vencimento");
			$("#cd_empresa").focus();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',  
			'RE', 
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'NumberFloat',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
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
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
	
		$participante['cd_empresa']            = $cd_empresa;
		$participante['cd_registro_empregado'] = $cd_registro_empregado;
		$participante['seq_dependencia']       = $seq_dependencia;
		$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
		
		echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');
		echo filter_participante( $conf, "Participante:", $participante, TRUE, FALSE );	
		echo filter_cpf('cpf', 'CPF:');
		echo filter_date_interval('dt_impressao_ini', 'dt_impressao_fim', 'Período da Dt Impressão:',calcular_data('','15 day'), date('d/m/Y'));
		echo filter_date_interval('dt_vencimento_ini', 'dt_vencimento_fim', 'Período da Dt Vencimento:');	
		echo filter_dropdown('fl_erro_registro', 'Status:', $ar_status);
	echo form_end_box_filter();	
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<?php
	echo br(8);
	echo aba_end(''); 
?>
<script type="text/javascript">
	filtrar();
</script>
<?php
	$this->load->view('footer');
?>