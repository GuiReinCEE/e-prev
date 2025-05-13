<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateBR",
			"Number",
			"DateBR",
			"DateBR",
			"CaseInsensitiveString",
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
		ob_resul.sort(3, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$this->load->helper('grid');

	$head = array(
		'Dt. Aprovação CD',
	    'Nº Ata',
		'Dt. Envio PREVIC',
		'Dt. Aprovação PREVIC',
		'Documento PREVIC',
		'Arquivo'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		$item['dt_aprovacao_cd'],
			$item['nr_ata_cd'],			
			$item['dt_envio_spc'], 
			$item['dt_aprovacao_spc'], 
		    array($item['ds_aprovacao_spc'], 'text-align:left;'),
			anchor(base_url().'up/estatuto/'.$item['arquivo'], '[arquivo]' , array('target' => '_blank'))
		);
	}

	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo '<h2 style="font-size:120%">Gerência Responsável : GC</h2>';
		echo '<h2 style="font-size:120%">Publicado no Site : Sim</h2>';
		echo $grid->render();
		echo br(2);
	echo aba_end();
?>