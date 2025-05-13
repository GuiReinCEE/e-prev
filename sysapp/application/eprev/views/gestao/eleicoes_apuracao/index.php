<?php
	set_title('Eleições - Apuração');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}

	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('gestao/eleicoes_apuracao/listar') ?>',
		{
			id_eleicao : ""
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
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'NumberFloat',
			'NumberFloat',
			'NumberFloat',
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

	$(function(){
		load(); 
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start($abas);
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>