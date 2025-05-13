<?php
	$head = array(
		'Ano/Número',
		'Processo',
		'Situação',
		'Relatório de Audit.',
		'Dt. Envio',
		'Qt. Item',
		'Qt. Encerrado',
		'Qt. Não Encerrado'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/plano_acao/itens/'.$item['cd_plano_acao'], $item['ds_ano_numero']),
			array($item['procedimento'], 'text-align:left;') ,
			array(nl2br($item['ds_situacao']), 'text-align:justify;'),
			array(nl2br($item['ds_relatorio_auditoria']), 'text-align:justify;'),
			($item['dt_envio_responsavel']!= '' ? $item['dt_envio_responsavel'] : ''),
			'<label class="badge badge-info">'.$item['qt_itens'].'</label>',
			'<label class="badge badge-important">'.$item['qt_encerrado'].'</label>',
			'<label class="badge badge-success">'.$item['qt_n_encerrado'].'</label>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>