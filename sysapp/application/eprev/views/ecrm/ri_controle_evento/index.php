<?php
set_title('Controle Eventos - Lista');
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
		
		$.post('<?php echo site_url("/ecrm/ri_controle_evento/listar"); ?>', 
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
			'Number',
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'Number',
			'Number',
			'Number',
			'Number',
			"DateTimeBR",
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
		ob_resul.sort(2, true);
	}
	
	function novoControle()
	{
		location.href = '<?php echo site_url("ecrm/ri_controle_evento/detalhe/0"); ?>';
	}	
	
	function irResumo()
	{
		location.href = '<?php echo site_url("ecrm/ri_controle_evento/resumo"); ?>';
	}	
	
	$(function() {
		filtrar();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_resumo', 'Resumo', FALSE, 'irResumo();');

echo aba_start($abas);
	$config['button'][] = array('Novo Controle', 'novoControle();');
	echo form_list_command_bar($config);		
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_text('ds_evento', "Evento: ","", "style='width:400px;'");
		echo form_default_dropdown_db("cd_controle_evento_tipo", "Tipo:", array('crm.controle_evento_tipo', 'cd_controle_evento_tipo', 'ds_controle_evento_tipo' ));		
		echo form_default_dropdown_db("cd_controle_evento_local", "Local:", array('crm.controle_evento_local', 'cd_controle_evento_local', 'ds_controle_evento_local' ));		
		echo filter_date_interval('dt_ini','dt_fim','Dt Evento:');
	echo form_end_box_filter();
	
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	
	echo br(5);
echo aba_end('');
$this->load->view('footer');
?>