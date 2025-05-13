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
		'Número',
		'Dt Súmula',
		'Dt Divulgação',
		'Súmula',
		'Resposta'
	);

	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			$item['nr_sumula'],
			$item['dt_sumula'],
			$item['dt_divulgacao'],
			anchor(site_url('gestao/sumula/sumula_pdf').'/'.$item['cd_sumula'], '[ver súmula]' , array('target' => '_blank')),
			anchor('gestao/sumula/pdf/'.$item['cd_sumula'], '[ver resposta]', array('target' => '_blank'))
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