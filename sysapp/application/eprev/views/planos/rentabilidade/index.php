<?php
set_title('Rentabilidade');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		if($("#cd_plano_empresa").val() == "")
		{
			alert("Por favor, selecione uma empresa e um plano");
		}
		else if($("#nr_ano").val() == "")
		{
			alert("Por favor, digite um ano");
		}
		else
		{
			$("#result_div").html("<?= loader_html() ?>");
			
			$.post( '<?= site_url('planos/rentabilidade/listar') ?>',
			$('#filter_bar_form').serialize(),
			function(data)
			{
				$("#result_div").html(data);
			});		
		}
	}

$(function(){
	filtrar();
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	
echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros', true);
		echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');
		echo filter_integer('nr_ano', 'Ano:', date('Y'));
	echo form_end_box_filter();	
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end('');

$this->load->view('footer');
?>