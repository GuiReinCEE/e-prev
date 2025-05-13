<?php
set_title('Email Marketing - Lista');
$this->load->view('header');
?>
<script>
		
	var qt_participante       = 0;
	var qt_email_aguarda_env  = 0;
	var qt_email_nao_env      = 0;
	var qt_email_env          = 0;
	var qt_email              = 0;
	var qt_visualizacao       = 0;
	var qt_visualizacao_unica = 0;

	function filtrar()
	{
		load();
	}

	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/ecrm/divulgacao/listar'); ?>',
		$('#filter_bar_form').serialize(),
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
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'DateTimeBR',
			'Number',
			'Number',
			'Number',
			'Number',
			'Number',
			'Number',
			'Number'			
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

	function nova_divulgacao()
	{
		location.href = '<?php echo site_url("ecrm/divulgacao/cadastro/0"); ?>';
	}
	
	function listar_estatistica(cd_divulgacao)
	{
		/*
		//$("#"+cd_divulgacao+"_dt_ultimo_email_enviado").html("<?php echo loader_html(); ?>");
		$("#"+cd_divulgacao+"_percentual_envio").html("<?php echo loader_html(); ?>");
		$("#"+cd_divulgacao+"_qt_email_aguarda_env").html("...");
		$("#"+cd_divulgacao+"_qt_email_env").html("...");
		$("#"+cd_divulgacao+"_qt_email_nao_env").html("...");
		$("#"+cd_divulgacao+"_qt_email").html("...");
		$("#"+cd_divulgacao+"_qt_visualizacao").html("...");
		$("#"+cd_divulgacao+"_qt_visualizacao_unica").html("...");
		$("#"+cd_divulgacao+"_qt_participante").html("...");
		
		
		$.post
		(
			'<?php echo site_url("/ecrm/divulgacao/listar_estatistica"); ?>',
			{
				cd_divulgacao : cd_divulgacao
			},
			function(data)
			{
				if(data)
				{
					//$("#"+cd_divulgacao+"_dt_ultimo_email_enviado").html(data.dt_ultimo_email_enviado);
					$("#"+cd_divulgacao+"_percentual_envio").html(data.percentual_envio);
					$("#"+cd_divulgacao+"_qt_email_aguarda_env").html(data.qt_email_aguarda_env);
					$("#"+cd_divulgacao+"_qt_email_env").html(data.qt_email_env);
					$("#"+cd_divulgacao+"_qt_email_nao_env").html(data.qt_email_nao_env);
					$("#"+cd_divulgacao+"_qt_email").html(data.qt_email);
					$("#"+cd_divulgacao+"_qt_visualizacao").html(data.qt_visualizacao);
					$("#"+cd_divulgacao+"_qt_visualizacao_unica").html(data.qt_visualizacao_unica);
					$("#"+cd_divulgacao+"_qt_participante").html(data.qt_participante);
					
					qt_participante       = parseInt(qt_participante)       + parseInt(data.qt_participante);
					qt_email_aguarda_env  = parseInt(qt_email_aguarda_env)  + parseInt(data.qt_email_aguarda_env);
					qt_email_nao_env      = parseInt(qt_email_nao_env)      + parseInt(data.qt_email_nao_env);
					qt_email_env          = parseInt(qt_email_env)          + parseInt(data.qt_email_env);
					qt_email              = parseInt(qt_email)              + parseInt(data.qt_email);
					qt_visualizacao       = parseInt(qt_visualizacao)       + parseInt(data.qt_visualizacao);
					qt_visualizacao_unica = parseInt(qt_visualizacao_unica) + parseInt(data.qt_visualizacao_unica);
					
					listar_estatistica_resumo();
				}
			},
			'json'
		);
		*/
	}

	function listar_estatistica_resumo()
	{
		$("#table-1-sort-totalizador-6").html(qt_email_aguarda_env);
		$("#table-1-sort-totalizador-7").html(qt_email_env);
		$("#table-1-sort-totalizador-8").html(qt_email_nao_env);
		$("#table-1-sort-totalizador-9").html(qt_email);
		$("#table-1-sort-totalizador-10").html(qt_visualizacao);
		$("#table-1-sort-totalizador-11").html(qt_visualizacao_unica);
		$("#table-1-sort-totalizador-12").html(qt_participante);
	}

	$(function(){
		$("#dt_divulgacao_inicio_dt_divulgacao_fim_shortcut").val("currentMonth");
		$("#dt_divulgacao_inicio_dt_divulgacao_fim_shortcut").change();
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start($abas);

		$config['button'][]=array('Nova divulgação', 'nova_divulgacao();');
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_date_interval('dt_divulgacao_inicio', 'dt_divulgacao_fim', 'Data de divulgação: ');
			echo filter_text('nome', 'Divulgação: ');
			echo filter_dropdown('cd_divisao', 'Gerência:', $arr_gerencia);
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(); 

$this->load->view('footer');
?>