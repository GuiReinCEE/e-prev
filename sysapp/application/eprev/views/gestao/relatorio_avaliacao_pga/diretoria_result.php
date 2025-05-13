<?php
	$head = array(
		'Ano/Trimestre',
		'Diretoria',
		'Dt. Assinatura',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
		$body[] = array(

			anchor('gestao/relatorio_avaliacao_pga/apresentacao/'.$item['cd_relatorio_avaliacao_pga'], $item['nr_ano'].'/'.sprintf('%02d', $item['nr_trimestre']), 'target="_blank"'),
			$item['diretoria'],
			$item['dt_assinado'],
			($item['dt_assinado'] == '' ? '<a href="'.site_url('gestao/relatorio_avaliacao_pga/assinatura/'.$item['cd_relatorio_avaliacao_pga'].'/'.$item['cd_relatorio_avaliacao_pga_diretoria']).'" target="_blank )">[assinar]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>