<?php
set_title('Indicador de Desempenho');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/indicador/cadastro/listar');?>',
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
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			null
		]);
		ob_resul.onsort = function()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for(var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(1, false);
	}

	function novo()
	{
		location.href='<?php echo site_url("indicador/cadastro/detalhe"); ?>';
	}

	function carregar_tabelas(cd_indicador)
	{
		$.post('<?php echo site_url( "indicador/cadastro/carregar_tabelas_ajax" ); ?>', 
		{
			cd_indicador : cd_indicador
		},
		function(data)
		{ 
			$("#div_indicador_"+cd_indicador).html(data); 
			$("#div_indicador_"+cd_indicador).toggle(); 
		});
	}
	
	function exportDadosSA()
	{
		if(confirm("ATENÇÃO\n\nSerá exportado os dados de todos os indicadores configurados.\n\nClique [Ok] para SIM e [Cancelar] para NÃO\n\n"))
		{
			window.open("https://www.e-prev.com.br/_a/ind-todos.php");
			return false;	
		}
	}	

	$(function(){
		filtrar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo', 'novo()');
$config['button'][] = array('Exportar Lançamentos SA', 'exportDadosSA()');

$fl_filtro[] = array('text' => 'Sim', 'value' => 'S');
$fl_filtro[] = array('text' => 'Não', 'value' => 'N');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('cd_indicador_grupo', 'Grupo: ', $ar_grupos);
		echo filter_processo('cd_processo', 'Processo:');
		echo filter_dropdown('cd_tipo', 'Tipo: ', $ar_tipo);
		echo filter_dropdown('cd_indicador_controle', 'Controle: ', $ar_controle);
		echo filter_dropdown('fl_igp', 'PE: ', $fl_filtro);
		echo filter_dropdown('fl_poder', 'PODER: ', $fl_filtro);
	echo form_end_box_filter();
	echo '<div id="result_div">Realize um filtro para exibir a lista</div>';
	echo br(5);
echo aba_end(); 
$this->load->view('footer');
