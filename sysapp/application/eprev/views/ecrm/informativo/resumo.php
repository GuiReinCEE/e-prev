 <?php 
set_title('Clipping Diário - Resumo');
$this->load->view('header'); 
?>
<script>

function filtrar()
{
	if($('#ano').val() != '')
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url("ecrm/informativo/listar_resumo"); ?>', 
		$('#filter_bar_form').serialize(),
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	else
	{
		alert('Informe o ano.');
	}
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString', 
		'Number', 
		'Number',
		'Number',
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
	ob_resul.sort(0, false);
}

function ir_lista()
{
	location.href='<?php echo site_url("ecrm/informativo"); ?>';
}

$(function(){
	filtrar();
});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista()');
$abas[] = array('aba_lista', 'Resumo', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter(); 
		echo filter_integer('ano', 'Ano :', date('Y'));
    echo form_end_box_filter();
	echo '
		<div id="result_div">'.
			br(2).'
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br(5);
echo aba_end();
$this->load->view('footer');
?>
<?php $this->load->view('footer'); ?>