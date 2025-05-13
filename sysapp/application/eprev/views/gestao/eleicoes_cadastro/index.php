<?php
	set_title('Eleições');
	$this->load->view('header');
?>
<script type="text/javascript">
	function filtrar()
	{
		load();
	}

	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo base_url() . index_page(); ?>/gestao/eleicoes_cadastro/listar',
		{
			id_eleicao : $('#id_eleicao').val()
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
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo filter_dropdown('id_eleicao', 'Eleição:', $ar_eleicoes);
		echo form_end_box_filter();
	echo aba_end();
?>

<div id="result_div"></div>
<br />

<?php $this->load->view('footer'); ?>.