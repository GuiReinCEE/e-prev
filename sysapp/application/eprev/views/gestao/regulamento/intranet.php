<script>
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			null,
			null,
			null,
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
		ob_resul.sort(3, true);
	}

	function ir(nr_aba)
    {
    	location.href = "<?= site_url('ecrm/intranet/pagina/INST/10413/') ?>/"+nr_aba;
    }  

	$(function(){
		configure_result_table();
	});
</script>
<?php

	if(intval($nr_aba) == 0)
	{
		$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
		$abas[] = array('aba_encerrado', 'Encerrado', FALSE, 'ir(1);');
	}
	else
	{
		$abas[] = array('aba_lista', 'Lista', FALSE, 'ir(0);');
		$abas[] = array('aba_encerrado', 'Encerrado', TRUE, 'location.reload();');
	}

	$this->load->helper('grid');

	$head = array(
		'Regulamento',
		'Ger. Responsável',
		'Pub. Site',
		'Aprovação CD',
		'Dt. Aprovação PREVIC',
		'Arquivo',
		'Doc. Aprovação',
		'Quadros Comparativos',
		'Dt. Encerramento Plano',
		'Versões Anteriores'
	);

	$body = array();

	$head2 = array(
		'Aprovação CD',
		'Dt. Aprovação PREVIC',
		'Arquivo',
		'Doc. Aprovação',
		'Quadros Comparativos'
	);

	foreach ($collection as $key => $item)
	{
		$body2 = array();

		foreach ($item['versoes_anteriores'] as $key2 => $item2) 
		{
			$body2[] = array(
				array('Data : '.$item2['dt_aprovacao_cd'].br().'Nr. Ata :'.$item2['nr_ata_cd'], 'text-align:left;'),
			    $item2['dt_aprovacao_previc'],
			    anchor(base_url().'up/regulamento/'.$item2['arquivo'], '[regulamento]', array('target' => '_blank')),
				(trim($item2['arquivo_aprovacao_previc']) != '' ? anchor(base_url().'up/regulamento/'.$item2['arquivo_aprovacao_previc'], '[arquivo]', array('target' => '_blank')) : ''),
				(trim($item2['arquivo_comparativo']) != '' ? anchor(base_url().'up/regulamento/'.$item2['arquivo_comparativo'], '[arquivo]', array('target' => '_blank')) : '')
			);
		}

		$grid2 = new grid();
		$grid2->id_tabela = 'table-2-'.$key;
		$grid2->view_count = false;
		$grid2->head = $head2;
		$grid2->body = $body2;

	  	$body[] = array(
			array($item['ds_regulamento_tipo'].br().'CNPB : '.$item['ds_cnpb'], 'text-align:left;'),
			$item['cd_gerencia_responsavel'],
			$item['ds_publicado_site'],
		    array('Data : '.$item['dt_aprovacao_cd'].br().'Nr. Ata :'.$item['nr_ata_cd'], 'text-align:left;'),
		    $item['dt_aprovacao_previc'],
		    anchor(base_url().'up/regulamento/'.$item['arquivo'], '[regulamento]', array('target' => '_blank')),
			(trim($item['arquivo_aprovacao_previc']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_aprovacao_previc'], '[arquivo]', array('target' => '_blank')) : ''),
			(trim($item['arquivo_comparativo']) != '' ? anchor(base_url().'up/regulamento/'.$item['arquivo_comparativo'], '[arquivo]', array('target' => '_blank')) : ''),
			$item['dt_encerramento_plano'],
			(count($item['versoes_anteriores']) > 0 ? $grid2->render() : '')
		);
	}

	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;

	$grid->col_oculta = ($fl_desligado == 'S' ?  array() : array(6));

	echo aba_start($abas);
		echo $grid->render();
		echo br(2);
	echo aba_end();
?>