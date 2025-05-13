<?php
set_title('Pesquisa - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url('/ecrm/operacional_enquete/listar');?>',
		{
			titulo     : $('#titulo').val(),
			dt_ini     : $('#dt_ini').val(),
			dt_fim     : $('#dt_fim').val(),
			cd_enquete : $('#cd_enquete').val()
		},
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
			'DateTimeBR',
			'DateTimeBR',
			'DateTimeBR'
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

	function novaPesquisa()
	{
		location.href = '<?php echo site_url("ecrm/operacional_enquete/cadastro");?>';
	}

	function duplicar(cd_enquete)
	{
		if(confirm('Duplicar?'))
		{
			location.href = '<?php echo site_url("ecrm/operacional_enquete/duplicar");?>/' + cd_enquete;
		}
	}
	
	$(function() {
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Nova Pesquisa', 'novaPesquisa();');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_text('titulo', 'Título:', '', 'style="width:350px;"');		
		echo filter_date_interval('dt_ini', 'dt_fim', 'Período:');
		echo filter_integer('cd_enquete', 'Código:');
	echo form_end_box_filter();	
	echo '<div id="result_div"></div>';
	echo br(5);
echo aba_end(); 
$this->load->view('footer');
?>