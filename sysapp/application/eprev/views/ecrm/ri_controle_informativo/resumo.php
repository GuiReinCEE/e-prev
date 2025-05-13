<?php
set_title('Controle Informativos - Resumo');
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
		
		$.post('<?php echo site_url("/ecrm/ri_controle_informativo/resumoListar"); ?>', 
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
			'CaseInsensitiveString',
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
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_controle_informativo"); ?>';
	}	
	
	$(function() {
		filtrar();
	});	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_resumo', 'Resumo', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();		
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_integer('nr_ano', "Ano:", date('Y'));
		echo form_default_dropdown_db("cd_controle_informativo_tipo", "Tipo:", array('crm.controle_informativo_tipo', 'cd_controle_informativo_tipo', 'ds_controle_informativo_tipo' ));
	echo form_end_box_filter();
	
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	
	echo br(5);
echo aba_end('');
$this->load->view('footer');
?>