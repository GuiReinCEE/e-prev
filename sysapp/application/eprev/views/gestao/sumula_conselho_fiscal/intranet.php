<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"DateBR",
			"DateBR", 
			null,
			null     

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
		ob_resul.sort(2, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$this->load->helper('grid');

	$head = array(
		'N�mero',
		'Dt S�mula',
		'Dt Divulga��o',
		'S�mula',
		'Resposta'
	);

	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['nr_sumula_conselho_fiscal'],
			$item['dt_sumula_conselho_fiscal'],
			$item['dt_divulgacao'],
			anchor(site_url('gestao/sumula_conselho_fiscal/sumula_pdf').'/'.$item['cd_sumula_conselho_fiscal'], '[ver s�mula]' , array('target' => '_blank')),
			anchor('gestao/sumula_conselho_fiscal/pdf/'.$item['cd_sumula_conselho_fiscal'], '[ver resposta]', array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
		echo $grid->render();
		echo br(2);
	echo aba_end();
?>