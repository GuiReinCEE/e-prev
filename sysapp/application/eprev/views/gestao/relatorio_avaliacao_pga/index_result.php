<?php
	$head = array(
		'Ano/Trimestre',
		'Dt. Atualização Apresentação',
		'Dt. Encerramento',
		'Usuário Encerramento',
		'',
		''
	);

	$body = array();

	foreach($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/relatorio_avaliacao_pga/indicador/'.$item['cd_relatorio_avaliacao_pga'], $item['nr_ano'].'/'.sprintf('%02d', $item['nr_trimestre'])),
			$item['dt_alteracao'],
			$item['dt_encerramento'],
			array($item['cd_usuario_encerramento'], 'text-align: left'),
			(intval($item['qt_diretores']) == 0 ? array(progressbar(0),'text-align:left;') : array(progressbar(((intval($item['qt_assinados']) * 100) / intval($item['qt_diretores']))),'text-align:left;')),
			($item['dt_apresentacao'] != '' ? '<a href="'.site_url('gestao/relatorio_avaliacao_pga/apresentacao/'.$item['cd_relatorio_avaliacao_pga']).'" target="_blank">[apresentação]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>