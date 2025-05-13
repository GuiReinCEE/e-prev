<?php
set_title('Email Marketing - E-mails');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/ecrm/divulgacao/listarEmail'); ?>',
			{
				cd_divulgacao : $('#cd_divulgacao').val(),
				fl_lido       : $('#fl_lido').val(),
				fl_retornou   : $('#fl_retornou').val(),
				qt_pagina     : $('#qt_pagina').val(),
				nr_pagina     : $('#nr_pagina').val(),
				dt_email_ini  : $('#dt_email_ini').val(),
				dt_email_fim  : $('#dt_email_fim').val(),
				dt_envio_ini  : $('#dt_envio_ini').val(),
				dt_envio_fim  : $('#dt_envio_fim').val(),				
				email_enviado : $('#email_enviado').val(),
				nome          : $('#nome').val()
			},
			function(data)
			{
				$("#result_div").html(data);
				configure_result_table();
			}
		);
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
		ob_resul.sort(0, false);
	}

	function ir_lista()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/cadastro/".intval($cd_divulgacao)); ?>';
	}

	function ir_email_enviado()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($cd_divulgacao)."/N"); ?>';
	}	

	function ir_email_retornados()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/emails/".intval($cd_divulgacao)."/S"); ?>';
	}
	
	function ir_tecnologia()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/tecnologia/".intval($cd_divulgacao)); ?>';
	}

	function ir_participante()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/participante/".intval($cd_divulgacao)); ?>';
	}		

	$(function(){
		if($('#qt_pagina').val() == "")
		{
			$('#qt_pagina').val(500);
		}
		
		if($('#nr_pagina').val() == "")
		{
			$('#nr_pagina').val(1);
		}
		
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
	$abas[] = array('aba_emails_enviados', 'E-mails enviados', ($fl_retornou ==  "N" ? TRUE : FALSE), 'ir_email_enviado();');
	$abas[] = array('aba_emails_retornados', 'E-mails retornados', ($fl_retornou ==  "S" ? TRUE : FALSE), 'ir_email_retornados()');
	$abas[] = array('aba_tecnologia', 'Tecnologia', FALSE, 'ir_tecnologia()');
	$abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante()');
	
	$ar_lido[] = array('value' => 'S', 'text' => 'Sim');
	$ar_lido[] = array('value' => 'N', 'text' => 'Não');	
	
	echo aba_start( $abas );
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo form_default_hidden('cd_divulgacao', 'cd_divulgacao:', intval($cd_divulgacao));
			echo form_default_hidden('fl_retornou', 'Retornado:', $fl_retornou);
			echo filter_date_interval('dt_email_ini', 'dt_email_fim', 'Dt E-mail:');
			echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt Envio:');
			echo filter_dropdown('fl_lido', 'Visualizado:', $ar_lido);
			echo filter_text('nome', 'Nome:');
			echo filter_text('email_enviado', 'E-mail:');
			echo filter_integer('qt_pagina', 'Qt por página:');
			echo filter_integer('nr_pagina', 'Página:');
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(''); 

$this->load->view('footer');
?>