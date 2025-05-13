<?php
set_title('Acompanhamento de Projetos');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('/atividade/acompanhamento/listar'); ?>',
		{
			dt_acompanhamento_ini : $('#dt_acompanhamento_ini').val(),
			dt_acompanhamento_fim : $('#dt_acompanhamento_fim').val(),
			dt_encerramento_ini   : $('#dt_encerramento_ini').val(),
			dt_encerramento_fim   : $('#dt_encerramento_fim').val()
		},
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
			'CaseInsensitiveString', 
			'DateBR', 
			'DateBR', 
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
		ob_resul.sort(4, true);
	}

	function novo()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro"); ?>';
	}
	
	$(function(){
		filtrar();
	})
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo acompanhamento', 'novo()');

echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_date_interval('dt_acompanhamento_ini', 'dt_acompanhamento_fim', 'Dt Acompanhamento:');
		echo filter_date_interval('dt_encerramento_ini', 'dt_encerramento_fim', 'Dt Encerramento:');
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end(); 
$this->load->view('footer');
?>