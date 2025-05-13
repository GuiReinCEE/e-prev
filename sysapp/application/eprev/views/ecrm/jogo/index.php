<?php
set_title('Jogo - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("current_page").value = 0;
		load();
	}
		
	function load()
	{
		document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
		$.post( '<?php echo base_url() . index_page(); ?>/ecrm/jogo/jogoListar'
			,{
				current_page: $('#current_page').val(),
				dt_inclusao_ini: $('#dt_inclusao_ini').val(),
				dt_inclusao_fim: $('#dt_inclusao_fim').val()
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
					"DateTimeBR",
					"DateTimeBR",
					"DateTimeBR",
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
		ob_resul.sort(4, true);
	}


	function novoJogo()
	{
		location.href='<?php echo site_url("/ecrm/jogo/detalhe"); ?>';
	}
	
	function irResumo()
	{
		location.href='<?php echo site_url("/ecrm/jogo/resumo"); ?>';
	}	
	
	$(function() {
		filtrar();
	});		
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_resumo', 'Resumo', FALSE, 'irResumo();');

echo aba_start( $abas );
	$config['button'][]=array('Novo Jogo', 'novoJogo();');

	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros',FALSE);
		echo form_default_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro');
	echo form_end_box_filter();
	
	echo '<div id="result_div"><br><br><span style="color:green;"><b>Realize um filtro para exibir a lista</b></span></div>';
	
	echo br(5);
echo aba_end('');
$this->load->view('footer');
?>