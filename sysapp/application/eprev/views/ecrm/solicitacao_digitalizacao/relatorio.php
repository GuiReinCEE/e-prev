<?php
set_title('Solicitação de Digitalização - Relatório');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		if($("#nr_ano").val() != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('ecrm/solicitacao_digitalizacao/relatorio_listar') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
			});	
		}
		else
		{
			$("#result_div").html("");
			alert("Informa o ano.");
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url('ecrm/solicitacao_digitalizacao') ?>';
	}

	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');

echo aba_start($abas);
	echo form_list_command_bar();	
	echo form_start_box_filter();
		echo filter_integer('nr_ano', 'Ano :', date('Y'));
	echo form_end_box_filter();	
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>