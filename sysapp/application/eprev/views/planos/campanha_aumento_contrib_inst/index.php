<?php
set_title('Campanha Aumento de Contribuição');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('planos/campanha_aumento_contrib_inst/listar') ?>",
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
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"Number",
			"DateBR",
			"DateTimeBR",
			"CaseInsensitiveString",
			"Number",
			"Number",
			"Number",
			"Number"
			
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
		ob_resul.sort(0, true);
	}

	function novo()
	{
		location.href = "<?= site_url('planos/campanha_aumento_contrib_inst/cadastro') ?>";
	}

	$(function(){

		if($("#dt_inclusao_ini").val() == "" && $("#dt_inclusao_fim").val() == "")
		{
			$("#dt_inclusao_ini").val("<?= date('01/01/Y') ?>");
			$("#dt_inclusao_fim").val("<?= date('31/12/Y') ?>");
		}

		filtrar();
	})
</script>

<?php
$abas[0] = array('aba_lista', 'Lista', true, 'location.reload();');

$config['button'][] = array('Nova Campanha', 'novo();');

echo aba_start($abas);
	echo form_list_command_bar($config);
	echo form_start_box_filter(); 
		echo form_default_dropdown('cd_empresa', 'Instituidor :', $instituidor);
		echo filter_date_interval('dt_inclusao_ini', 'dt_inclusao_fim', 'Dt. Inclusão :');
		echo filter_date_interval('dt_envio_ini', 'dt_envio_fim', 'Dt. Envio :');
		echo filter_dropdown('dt_envio', "Envio :", array(array('value' => 'N', 'text' => 'Não'), array('value' => 'S', 'text' => 'Sim')));
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>