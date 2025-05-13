<?php
set_title('SVN - Fontes');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('servico/svn_estatistica/listar');?>',
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			//configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number", 
			"DateBR", 
			"CaseInsensitiveString", 
			null, 
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
					

	$(function(){
		filtrar();
	});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_tipo[] = array('value' => 'ORACLE', 'text' => 'Oracle');
$arr_tipo[] = array('value' => 'WEB', 'text' => 'Web');
$arr_tipo[] = array('value' => 'VB', 'text' => 'Visual Basic');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo form_default_dropdown('ds_repositorio', 'Tipo :', $arr_tipo, array($tipo));
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>