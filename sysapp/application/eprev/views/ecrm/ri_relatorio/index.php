<?php
	set_title('GRI - Relatório');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		if($("#nr_ano").val() != "")
		{
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_relatorio/listar',
				{
					cd_empresa : $("#cd_plano_empresa").val(),
					cd_plano   : $("#cd_plano").val(),
					nr_ano     : $("#nr_ano").val()
				}
				,
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				}
			);
		}
		else
		{
			alert("Informe o ano e clique em filtrar");
			$("#nr_ano").focus();
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',  
			'Number',  
			'Number',  
			'Number',  
			'Number',  
			'NumberFloat',  
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
		ob_resul.sort(0, false);
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo filter_plano_ajax('cd_plano', '', '', 'Empresa:', 'Plano:');
		echo filter_integer('nr_ano', "Ano:");
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