<?php
set_title('SMS Marketing - Lista');
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

		$.post('<?php echo site_url('/sms/sms_divulgacao/listar'); ?>',
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
			'DateTimeBR',
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

	function nova_divulgacao()
	{
		location.href = '<?php echo site_url("sms/sms_divulgacao/cadastro/0"); ?>';
	}

	$(function(){
		$("#dt_inclusao_inicio_dt_inclusao_fim_shortcut").val("currentMonth");
		$("#dt_inclusao_inicio_dt_inclusao_fim_shortcut").change();
		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start($abas);

		$config['button'][]=array('Nova divulgação', 'nova_divulgacao();');
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
			echo filter_date_interval('dt_inclusao_inicio', 'dt_inclusao_fim', 'Data de divulgação: ');
			echo filter_text('ds_assunto', 'Assunto: ');
			echo filter_text('ds_conteudo', 'Texto: ');
		echo form_end_box_filter();
		echo '<div id="result_div" align="center"><BR><BR><span class="label label-success">Realize um filtro para exibir a lista</span></div>';
		echo br(5);
	echo aba_end(); 

$this->load->view('footer');
?>