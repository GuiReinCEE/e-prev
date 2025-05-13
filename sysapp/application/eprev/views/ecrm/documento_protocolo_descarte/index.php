<?php
set_title('Protocolo Digitalização - Descarte de Documentos');
$this->load->view('header');
?>
<script>
	$(function(){
	   filtrar(); 
	})

	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('/ecrm/documento_protocolo_descarte/listar'); ?>',
		{
			cd_gerencia : $('#cd_gerencia').val(),
			fl_descarte : $('#fl_descarte').val()
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
			'DateTimeBR',
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
		ob_resul.sort(0, false);
	}

	function novo()
	{
		location.href='<?php echo site_url("ecrm/documento_protocolo_descarte/cadastro"); ?>';
	}
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][]=array('Novo', 'novo()');

$arr[] = array('text' => 'Sim', 'value' => 'S');
$arr[] = array('text' => 'Não', 'value' => 'N');

echo aba_start( $abas );
    echo form_list_command_bar($config);
	echo form_start_box_filter();
		echo filter_dropdown('cd_gerencia', 'Gerência:', $arr_gerencias, $this->session->userdata('divisao'));
		echo filter_dropdown('fl_descarte', 'Descarta :', $arr);
    echo form_end_box_filter();
echo aba_end();
echo '<div id="result_div"></div>';
echo br();

$this->load->view('footer'); ?>.