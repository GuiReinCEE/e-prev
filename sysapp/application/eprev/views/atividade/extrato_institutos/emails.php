<?php
	set_title('Extrato Institutos - Emails');
	$this->load->view('header');
?>
<script>	
	function filtrar()
    {
		if($("#dt_email_ini").val() != "" && $("#dt_email_fim").val() != "")
		{
			$("#result_div").html("<?= loader_html() ?>");

			$.post("<?= site_url('atividade/extrato_institutos/emails_listar') ?>", 
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});
		}
		else
		{
			alert("Informe o Período do Email.");
			$("#result_div").html('<br/><br/><span class="label label-success">Informe o Período da Emissão do E-mail</span>');
		}
    }
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'Number',
			'RE',
			'CaseInsensitiveString',
			'DateTimeBR',
			'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
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

	
	function ir_lista()
	{
		location.href = "<?= site_url('atividade/extrato_institutos') ?>";
	}
	
	$(function(){
		if(($("#dt_email_ini").val() == "") || ($("#dt_email_fim").val() == ""))
		{
			$("#dt_email_ini_dt_email_fim_shortcut").val("currentMonth");
			$("#dt_email_ini_dt_email_fim_shortcut").change();
		}
		
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Enviar', false, 'ir_lista();');
	$abas[] = array('aba_email', 'Emails', TRUE, 'location.reload();');

	$arr = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	$participante = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Dt. Email: (*)');
			echo filter_dropdown('fl_retornou', 'Retornado:', $arr);
			echo filter_participante($participante, 'Participante:', array(), TRUE, FALSE);	
		echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end(); 
		 
	$this->load->view('footer_interna');
?>