<?php
set_title('Família Previdência - Delegacia Cidade - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		$.post( '<?php echo base_url() . index_page(); ?>/planos/familia_previdencia_delegacia_cidade/listar',
		{
			cd_delegacia : $("#cd_delegacia").val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		}
		);
	}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
				"Number",
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


function novo()
{
	location.href='<?php echo base_url().index_page(); ?>/planos/familia_previdencia_delegacia_cidade/cadastro';
}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Nova Cidade', 'novo()');
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros', TRUE);
		echo filter_dropdown('cd_delegacia', 'Delegacia:', $delegacia_dd);			
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