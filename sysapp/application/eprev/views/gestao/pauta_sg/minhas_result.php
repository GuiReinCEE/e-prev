<?php
	$head = array( 
		'Nº da Ata',
		'Colegiado',
		'Tipo Reunião',
		'Local',
		'Dt. Reunião',
		'Dt. Reunião Encerramento',
		'Assunto',
		'Tempo (mim)',
		'Qt. Arquivos',
		'Dt. Limite'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('gestao/pauta_sg/responder/'.$item['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], $item['nr_ata']),
			'<span class="'.$item['class_sumula'].'">'.$item['fl_sumula'].'</span>',
			$item['ds_tipo_reuniao'],
			array($item['local'], 'text-align:left;'),
			anchor('gestao/pauta_sg/responder/'.$item['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'],  $item['dt_pauta']),
			$item['dt_pauta_sg_fim'],
			array($item['ds_pauta_sg_assunto'], 'text-align:left;'),
			$item['nr_tempo'],
			$item['tl_arquivo'],
			'<span class="'.trim($item['ds_class_limite']).'">'.$item['dt_limite'].'</span>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>