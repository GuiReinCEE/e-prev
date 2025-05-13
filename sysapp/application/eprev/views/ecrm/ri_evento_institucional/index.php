<?php
	set_title('Eventos Institucionais - Lista');
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

		$.post('<?php echo site_url("/ecrm/ri_evento_institucional/listar"); ?>',
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
			'Number',  
			'CaseInsensitiveString', 
			'DateBR', 
			'Number', 
			'Number', 
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

	function novo()
	{
		location.href='<?php echo site_url("ecrm/ri_evento_institucional/detalhe/0"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Novo', 'novo()');
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros', FALSE);
		echo filter_text('nome', 'Nome');
	echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
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