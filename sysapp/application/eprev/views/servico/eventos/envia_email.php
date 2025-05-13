<?php
set_title('Eventos - E-mails Enviado');
$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href = "<?= site_url('servico/eventos') ?>";
    }

	function ir_cadastro()
    {
        location.href = "<?= site_url('servico/eventos/cadastro/'.intval($cadastro['cd_evento'])) ?>";
    }
	
	function filtrar()
	{
		var dt_envio_ini = $("#dt_envio_ini").val();
		var dt_envio_fim = $("#dt_envio_fim").val();

		if(dt_envio_ini != '' && dt_envio_fim != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
				
			$.post("<?= site_url('servico/eventos/envia_email_listar/'.$cadastro['cd_evento']) ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			});	
		}
		else
		{
			alert("Informe data de envio");
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[	
			"Number",
			"CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR",
		    "CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}

	$(function(){
		$("#dt_envio_ini_dt_envio_fim_shortcut").val("last30days");
		$("#dt_envio_ini_dt_envio_fim_shortcut").change();

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_emails_enviado', 'E-mails Enviado', TRUE, 'location.reload();');
	
	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio:');
			echo filter_date_interval('dt_email_enviado_ini', 'dt_email_enviado_fim', 'Dt. Email Enviado:');			
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>