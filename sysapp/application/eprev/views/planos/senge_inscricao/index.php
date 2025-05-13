<?php
set_title('Inscrições no SENGE');
$this->load->view('header');
?>
<script>

function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('planos/senge_inscricao/listar')?>',$('#filter_bar_form').serialize(),
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
		'DateBR',
		'DateBR'
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

$option[] = array('value'=>'', 'text'=>'Todos');
$option[] = array('value'=>'1', 'text'=>'Pendentes');
$option[] = array('value'=>'2', 'text'=>'Confirmados');
$option[] = array('value'=>'3', 'text'=>'Participante Ativo');

echo aba_start( $abas );

	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('situacao', 'Situação', $option);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);

echo aba_end(); 

$this->load->view('footer');
?>