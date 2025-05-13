<?php
set_title('Solicitação de Digitalização');
$this->load->view('header');
?>
<script>
	function novo()
	{
		location.href = "<?= site_url('ecrm/solicitacao_digitalizacao/cadastro') ?>";
	}

	function ir_relatorio()
	{
		location.href = '<?= site_url('ecrm/solicitacao_digitalizacao/relatorio') ?>';
	}

	function get_usuarios(cd_gerencia)
	{
		$.post("<?= site_url('ecrm/solicitacao_digitalizacao/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			var select = $('#cd_usuario_responsavel'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
		}, 'json', true);
	}

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/solicitacao_digitalizacao/listar') ?>",
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
			'DateBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
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

	$(function(){
		if($("#dt_solicitacao_digitalizacao_ini").val() == '' || $("#dt_solicitacao_digitalizacao_fim").val() == '')
		{
			$("#dt_solicitacao_digitalizacao_ini_dt_solicitacao_digitalizacao_fim_shortcut").val("currentMonth");
			$("#dt_solicitacao_digitalizacao_ini_dt_solicitacao_digitalizacao_fim_shortcut").change();
		}

		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');

$config['button'][] = array('Solicitação de Digitalização', 'novo();');

echo aba_start($abas);
	echo form_list_command_bar($config);	
	echo form_start_box_filter();
		echo filter_date_interval('dt_solicitacao_digitalizacao_ini', 'dt_solicitacao_digitalizacao_fim', 'Data :');
		echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência: ', '','onchange="get_usuarios(this.value)"');
		echo form_default_dropdown('cd_usuario_responsavel', 'Usuário: ', array());	
	echo form_end_box_filter();	
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>