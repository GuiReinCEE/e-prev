<?php
set_title('Emails Hoje');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#result_div").html("<?= loader_html() ?>");

	$.post("<?= site_url('/servico/emails_hoje/listar') ?>",
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
		"Number",
		"DateTimeBR",
		"DateTimeBR",
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
	ob_resul.sort(1, true);
}
$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	
echo aba_start($abas);
	echo form_list_command_bar();
    echo form_start_box_filter();
		echo filter_text('assunto', 'Assunto: ', '', 'style="width:300px;"');
		echo filter_dropdown('fl_enviado', 'Enviado:', $fl_enviado);
		echo filter_dropdown('cd_evento', 'Evento:', $cd_evento);    
		echo filter_dropdown('cd_divulgacao', 'Divulga��o:', $cd_divulgacao);    
	echo form_end_box_filter();

	echo '<div id="result_div"></div>';
	echo br(2);	
echo aba_end();

$this->load->view('footer');
?>