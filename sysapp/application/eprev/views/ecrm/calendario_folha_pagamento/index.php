<?php
set_title('Calendário de folha de pagamento');
$this->load->view('header');
?>
<script>
	function novo()
	{
		location.href = "<?= site_url('ecrm/calendario_folha_pagamento/cadastro') ?>";
	}

	function filtrar()
	{	
		if($("#nr_ano").val() != '')
		{
			$("#result_div").html("<?= loader_html() ?>");
					
			$.post("<?= site_url('ecrm/calendario_folha_pagamento/listar') ?>",
				$('#filter_bar_form').serialize(),
				function(data)
				{
					$("#result_div").html(data);
					configure_result_table();
				});
		}
		else
		{
			alert("Informe o Ano antes de filtrar");
		}
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'DateBR',
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

	$(function(){
		filtrar();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Cadastro', 'novo();');

echo aba_start($abas);
	echo form_list_command_bar($config);	
		echo form_start_box_filter();
			echo filter_integer('nr_ano', 'Ano :', date('Y'));
		echo form_end_box_filter();	
		echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>