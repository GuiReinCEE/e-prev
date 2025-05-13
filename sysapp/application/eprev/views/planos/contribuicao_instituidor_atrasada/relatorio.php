<?php
	set_title('Contribuição instituidor - Relatório Contribuição Atrasada');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo base_url() . index_page(); ?>/planos/contribuicao_instituidor_atrasada/relatorioListar',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_mes     : $("#nr_mes").val(),
					nr_ano     : $("#nr_ano").val(),
					cd_registro_empregado : $("#cd_registro_empregado").val(),
					seq_dependencia       : $("#seq_dependencia").val(),
					fl_retornou           : $("#fl_retornou").val()
				}
				,
				function(data)
				{
					document.getElementById("result_div").innerHTML = data;
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}

	function envia_email_retorno()
	{
		if(($("#cd_plano_empresa").val() != "") && ($("#cd_plano").val() != "") && ($("#nr_mes").val() != "") && ($("#nr_ano").val() != ""))
		{
			location.href='<?php echo site_url("planos/contribuicao_instituidor_atrasada/envia_email_retorno"); ?>/'+$("#cd_plano").val()+'/'+$("#cd_plano_empresa").val()+'/'+$("#nr_ano").val()+'/'+$("#nr_mes").val();
		}
		else
		{
			alert("Informe os campos com (*) e clique em filtrar");
			$("#cd_plano_empresa").focus();
		}
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"RE",
					"CaseInsensitiveString",
					"DateTimeBR",
					"DateTimeBR",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString"
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
		ob_resul.sort(1, false);
	}	
	
	function ir_atrasada()
	{
		location.href='<?php echo site_url("planos/contribuicao_instituidor_atrasada"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Contribuição Atrasada', FALSE, 'ir_atrasada();');
	$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo filter_plano_ajax('cd_plano', $cd_plano_empresa, $cd_plano, 'Empresa:(*)', 'Plano:(*)','I');
		echo filter_integer('nr_mes', "Mês:(*)",(intval($nr_mes) > 0 ? intval($nr_mes) : date('m')));
		echo filter_integer('nr_ano', "Ano:(*)",(intval($nr_ano) > 0 ? intval($nr_ano) : date('Y')));
		
		$participante['cd_empresa']            = null;
		$participante['cd_registro_empregado'] = null;
		$participante['seq_dependencia']       = null;
		$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');
		echo filter_participante( $conf, "Participante:", $participante, TRUE, FALSE );		

		$ar_status_email = Array(Array('text' => 'Retornou', 'value' => 'S'),Array('text' => 'Normal', 'value' => 'N')) ;
		echo filter_dropdown('fl_retornou', 'Situação Email:', $ar_status_email,$fl_retornou);		
	echo form_end_box_filter();	
?>
<div id="result_div"><br><br><span style='color:green;'><b>Clique no botão [Filtrar] para exibir as informações</b></span></div>
<br>
<?php
	echo aba_end(''); 
?>
<script type="text/javascript">
	filtrar();
</script>
<?php
	$this->load->view('footer');
?>