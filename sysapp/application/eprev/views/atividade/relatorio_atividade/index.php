<?php
set_title('Relatório Atividades');
$this->load->view('header');
?>
<script>
	function filtrar()
    {
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post('<?= site_url('atividade/relatorio_atividade/listar') ?>', 
		$('#filter_bar_form').serialize(), 
		function(data) 
		{
			$("#result_div").html(data);
		});
    }

function pdf()
{
	filter_bar_form.method = "post";
	filter_bar_form.action = '<?= site_url('atividade/relatorio_atividade/pdf') ?>';
	filter_bar_form.target = "_blank";
	filter_bar_form.submit();
}

$(function(){
	var gerenciaCheck = $("#gerencia_row").find("input:checkbox");
	var statusCheck = $("#status_row").find("input:checkbox");
	var fl_marca_todos_gerencia = true;
	var fl_marca_todos_status = true;
	
	jQuery.each(gerenciaCheck, function(){
		if ((this.checked == true) && (this.id != "gerencia_checkall"))
		{
			fl_marca_todos_gerencia = false;
		}
	});		
	
	if (fl_marca_todos_gerencia == true)
	{		
		jQuery.each(gerenciaCheck, function(){
			this.checked = true;
		});
	}
	
	jQuery.each(statusCheck, function(){
		if ((this.checked == true) && (this.id != "status_checkall"))
		{
			fl_marca_todos_status = false;
		}
	});		
	
	if (fl_marca_todos_status == true)
	{		
		jQuery.each(statusCheck, function(){
			this.checked = true;
		});
	}
	
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('PDF', 'pdf()');

$status[] = array('value' => 'AINI', 'text' => 'Aguardando Início');
$status[] = array('value' => 'AUSR', 'text' => 'Aguardando Usuário');	
$status[] = array('value' => 'EMAN', 'text' => 'Em manutenção');
$status[] = array('value' => 'ETES', 'text' => 'Em Testes');

echo aba_start($abas);
		echo form_list_command_bar($config);
		echo form_start_box_filter('filter_bar', 'Filtros'); 
			echo filter_checkbox_group('gerencia', 'Gerência:', $gerencia);
			echo filter_checkbox_group('status', 'Status:', $status);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

$this->load->view('footer');
?>