<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
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
		ob_resul.sort(4, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$this->load->helper('grid');

	$head = array(
		'Versão',
		'Política',
		'Ger. Responsável',
		'Pub. Site',
		'Dt. Aprovação',
		'Arquivo',
		'Versões Anteriores'
	);

	$body = array();

	$head2 = array(
		'Versão',
		'Dt. Aprovação',
		'Arquivo'
	);

	foreach ($collection as $key => $item)
	{
		$body2 = array();

		foreach ($item['versoes_anteriores'] as $key2 => $item2) 
		{
			$body2[] = array(
				$item2['nr_versao'],
				$item2['dt_referencia'],
				anchor(base_url().'up/politica/'.$item2['arquivo'], '[arquivo]', array('target' => '_blank'))
			);
		}

		$grid2 = new grid();
		$grid2->id_tabela = 'table-2-'.$key;
		$grid2->view_count = false;
		$grid2->head = $head2;
		$grid2->body = $body2;

	  	$body[] = array(
			$item['nr_versao'],
		    array($item['ds_politica_tipo'], 'text-align:left;'),
		    $item['cd_gerencia_responsavel'],
		    $item['ds_publicado_site'],
			$item['dt_referencia'],
			anchor(base_url().'up/politica/'.$item['arquivo'], '[arquivo]', array('target' => '_blank')),
			(count($item['versoes_anteriores']) > 0 ? $grid2->render() : '')
			
		);
	}

	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo $grid->render();
		echo br(2);
	echo aba_end();
?>