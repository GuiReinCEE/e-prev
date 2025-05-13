<?php
	set_title('Arquivos Contracheque');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/contracheque_arquivo/listar') ?>",
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
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateTimeBR",
			"Number",
			"Number",
			"DateBR",
			"DateTimeBR"
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
		location.href = "<?= site_url('cadastro/contracheque_arquivo/cadastro') ?>";
	}

	$(function(){
		filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['filter'] = FALSE;
	$config['button'][] = array('Cadastro', 'novo()');

	$head = array( 
		'Ano/Mês',
		'Arquivo',
		'Dt. Pagamento',
		'Dt. Carga',
		'Qt. Linha',
		'Qt. RE',
		'Dt. Liberação',
		'Dt. Envio E-mail'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['dt_referente'],
			array($item['ds_arquivo_nome'], 'text-align:left;'),
			$item['dt_pagamento'],
			$item['dt_upload'],
			$item['qt_linha'],
			$item['qt_registro_empregado'],
			$item['dt_liberacao'],
			$item['dt_envio_email']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo $grid->render();
		echo br();
	echo aba_end();

	$this->load->view('footer');
?>