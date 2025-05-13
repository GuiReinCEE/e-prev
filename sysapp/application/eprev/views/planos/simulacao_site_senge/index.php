<?php
set_title('Simulação - Site');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('planos/simulacao_site_senge/listar');?>',
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
			"DateTimeBR",
			"CaseInsensitiveString",
			"Number",
			"CaseInsensitiveString",
			"CaseInsensitiveString", 
			null
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
		filtrar();
	})
</script>

<?php
$abas[0] = array('aba_lista', 'Lista', true, 'location.reload();');

echo aba_start( $abas );
	echo form_list_command_bar();
	echo form_start_box_filter(); 
		echo filter_text('nome', 'Nome :', '', 'style="width:400px;"');
		echo filter_dropdown('fl_simulacao', "Acompanhamento :", array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')));
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>