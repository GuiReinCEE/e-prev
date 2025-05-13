<?php
set_title('SENGE - Inscrição');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
	$.post( '<?php echo site_url('planos/senge_previdencia_inscricao/listar');?>', $('#filter_bar_form').serialize(),
	function(data)
	{
		$("#result_div").html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
		"Number",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"DateTimeBR",
		"DateBR",
		"DateBR"
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

$arr_yes_or_no[] = array('value' => 'S', 'text' => 'Sim');
$arr_yes_or_no[] = array('value' => 'N', 'text' => 'Não');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter();
		echo filter_date_interval('dt_inscricao_ini', 'dt_inscricao_fim', 'Dt Inscrição :');
		echo filter_date_interval('dt_inclusao_gap_ini', 'dt_inclusao_gap_fim', 'Dt Cadastrado pela GAP :');
		echo filter_date_interval('dt_ingresso_eletro_ini', 'dt_ingresso_eletro_fim', 'Dt Ingresso Participante :');
        echo filter_dropdown('fl_cadastro_gap', 'Cadastrado pela GAP :', $arr_yes_or_no);
        echo filter_dropdown('fl_participante', 'Participante :', $arr_yes_or_no);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer');
?>