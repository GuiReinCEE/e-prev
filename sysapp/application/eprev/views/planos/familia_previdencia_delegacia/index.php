<?php
set_title('Fam�lia Previd�ncia - Delegacia - Lista');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		$.post( '<?php echo base_url() . index_page(); ?>/planos/familia_previdencia_delegacia/listar',
		{},
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
				"CaseInsensitiveString",
				"CaseInsensitiveString",
				"CaseInsensitiveString",
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
	location.href='<?php echo base_url().index_page(); ?>/planos/familia_previdencia_delegacia/cadastro';
}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['button'][]=array('Nova Delegacia', 'novo()');
	$config['filter']=false;
	echo form_list_command_bar($config);
	
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