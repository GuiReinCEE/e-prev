<?php
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);

$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('/indicador_plugin/atend_satisfacao_seminario_seguridade/listar'); ?>',
		function(data)
		{ 
			$('#result_div').html(data);
			configure_result_table(); 
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
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

		ob_resul.sort(0, false);
	}

	function gerar_grafico()
	{
		if(confirm('Atualizar apresentação?'))
		{
			$.post('<?php echo site_url("indicador_plugin/atend_satisfacao_seminario_seguridade/criar_indicador/"); ?>', function(){});
		}
	}

	function novo()
	{
		location.href='<?php echo site_url("indicador_plugin/atend_satisfacao_seminario_seguridade/cadastro"); ?>';
	}

	function manutencao()
	{
		location.href='<?php echo site_url("indicador/manutencao/"); ?>';
	}

	$(function(){
		filtrar();
	});
</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array( 'aba_lista', 'Lançamento', true, 'location.reload();' );

$config['filter'] = false;

$config['button'][] = array('Informar valores', 'novo()');
$config['button'][] = array('Atualizar apresentação', 'gerar_grafico()');

echo aba_start( $abas );
	echo "<div id='div-output'></div>";
	echo form_list_command_bar($config, TRUE);
	/*
	echo form_start_box_filter('filter_bar', 'Filtros');
	echo form_end_box_filter();
	*/
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 
$this->load->view('footer');
?>
