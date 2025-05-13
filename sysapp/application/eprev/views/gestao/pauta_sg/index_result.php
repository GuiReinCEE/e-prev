<?php
	$head = array( 
		'N� da Ata',
		'Colegiado',
		'Tipo Reuni�o',
		'Local',
		'Dt. Reuni�o',
		'Dt. Reuni�o Encerramento',
		'Dt. Encerramento',
		'Usu�rio',
		'Qt. Itens', 
		'Qt. Itens Decididos',
		'Qt. Itens Removidos',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('gestao/pauta_sg/assunto/'.$item['cd_pauta_sg'], $item['nr_ata']),
			'<span class="'.$item['class_sumula'].'">'.$item['fl_sumula'].'</span>',
			$item['ds_tipo_reuniao'],
			array($item['local'], 'text-align:left;'),
			anchor('gestao/pauta_sg/assunto/'.$item['cd_pauta_sg'], $item['dt_pauta']),
			$item['dt_pauta_sg_fim'],
			$item['dt_aprovacao'],
			array($item['ds_usuario_aprovacao'], 'text-align:left;'),
			'<span class="badge badge-success">'.$item['tl_itens'].'</span>',
			'<span class="badge badge-info">'.$item['tl_itens_decisao'].'</span>',
			'<span class="badge badge-warning">'.$item['tl_itens_retirada'].'</span>',
			anchor('gestao/pauta_sg/pauta/'.$item['cd_pauta_sg'], '[pauta]', array('target' => '_blank')).' '.
			anchor('gestao/pauta_sg/sumula/'.$item['cd_pauta_sg'], '[s�mula]', array('target' => '_blank'))
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>