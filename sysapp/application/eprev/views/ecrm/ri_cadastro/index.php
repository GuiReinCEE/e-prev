<?php
set_title('(GRI) Cadastro - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
		
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/ri_cadastro/listar'
			,{
				cd_cadastro_origem : $('#cd_cadastro_origem').val(),
				nome               : $('#nome').val(),
				empresa            : $('#empresa').val()
			}
			,
		function(data)
			{
				document.getElementById("result_div").innerHTML = data;
				configure_result_table();
			}
		);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
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
		ob_resul.sort(1, false);
	}
</script>
<?php
	$abas[0] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );
	echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros', false);
		echo filter_dropdown('cd_cadastro_origem', 'Origem:', $ar_cadastro_origem);	
		echo filter_text('nome', "Nome: ","", "style='width:400px;'");
		echo filter_text('empresa', "Empresa: ","", "style='width:100%;'");
	echo form_end_box_filter();
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br />
<?php echo aba_end(''); ?>
<script>
	filtrar();
</script>
<?php
$this->load->view('footer');
?>