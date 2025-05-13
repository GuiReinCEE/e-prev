<?php
set_title('Plano Único CGTEE - Pré-Cadastro');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo site_url('/planos/plano_unico_cgtee_interesse/listar');?>',
		$('#filter_bar_form').serialize(),
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
		"Number",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"DateTimeBR",
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
	
	$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").val('last30days');
	$("#dt_inclusao_ini_dt_inclusao_fim_shortcut").change();
	
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$arr_status[] = array('value' => 'C', 'text' => 'Contato');
$arr_status[] = array('value' => 'A', 'text' => 'Aguardando');

echo aba_start( $abas );
	echo form_list_command_bar(array());
	echo form_start_box_filter();
        echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt Cadastro :');
		echo filter_dropdown('fl_status', 'Status :', $arr_status);
    echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end('');
$this->load->view('footer');
?>