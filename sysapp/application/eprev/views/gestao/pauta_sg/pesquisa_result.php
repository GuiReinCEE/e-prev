<?php
	$head = array( 
		'Número da Ata',
		'Colegiado',
		'Tipo Reunião',
		'Dt. Reunião',
		'Dt. Reunião Encerramento',
		'Diretoria',
		'Assunto',
		'Decisão',
		'Qt. Anexo',
		''
	);

	$body = array();

	foreach( $collection as $item )
	{
		$body[] = array(
			anchor('gestao/pauta_sg/assunto/'.$item['cd_pauta_sg'], $item['nr_ata']),
			'<span class="'.$item['class_sumula'].'">'.$item['fl_sumula'].'</span>',
			$item['ds_tipo_reuniao'],
			$item['dt_pauta'],		
			$item['dt_pauta_sg_fim'],
			array($item['ds_diretoria'], 'text-align:left;'),
			array(nl2br($item['ds_pauta_sg_assunto']), 'text-align:justify;'),
			array(nl2br($item['ds_decisao']), 'text-align:justify;'),
			$item['qt_anexo'],
			anchor('gestao/pauta_sg/anexo/'.$item['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[arquivos]').
			(intval($item['qt_anexo']) > 0 ? br().anchor('gestao/pauta_sg/zip_docs/'.$item['cd_pauta_sg'].'/'.$item['cd_pauta_sg_assunto'], '[download zip]', 'taget="_blank"') : "")
		);
	}
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>