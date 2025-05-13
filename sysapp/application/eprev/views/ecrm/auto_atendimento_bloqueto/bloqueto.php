<?php
set_title('Bloqueto (Autoatendimento)');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		document.getElementById("current_page").value = 0;
		load();
	}
	
	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		

		$.post('<?php echo site_url('ecrm/auto_atendimento_bloqueto/listar_bloqueto'); ?>',
		{
			current_page: $('#current_page').val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					  "CaseInsensitiveString"
					, "Number"
					, "NumberFloatBR"
					, "DateBR"
					, "DateBR"
					, "DateTimeBR"
					, "CaseInsensitiveString"
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
		ob_resul.sort(0, false);
	}

	function ir_lista_arquivo()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_bloqueto"); ?>';
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista',    FALSE, 'ir_lista_arquivo();');
	$abas[] = array('aba_lista', 'Bloquetos Disponível', TRUE, 'location.reload();');
	echo aba_start( $abas );

	$config['filter'] = FALSE;

	echo form_list_command_bar($config);
?>
<div id="result_div"></div>
<br />
<?php echo aba_end( ''); ?>
<script type="text/javascript">
	filtrar();
</script>
<?php
$this->load->view('footer');
?>