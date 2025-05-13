<?php
	$head = array(
		'Edição',
		'Empresa',
		'Plano',
		'Tipo',
		'Nº Extrato',
		'Dt. Base',
		'Qt. Participantes',
		'Dt. Informática',
        'Usuário',
        'Dt. Atuarial/Benefício',
        'Usuário',
        'Dt. Comunicação',
        'Usuário'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			'<a href="javascript:void(0)" onclick="cadastro('.$item['cd_edicao'].')">'.$item['cd_edicao'].'<a/>',
			array('<a href="javascript:void(0)" onclick="cadastro('.$item['cd_edicao'].')">'.$item['sigla'].'<a/>', 'text-align:left'),
			array($item['plano'], 'text-align:left'),
			'<span class="label '.trim($item['class_tipo_participante']).'">'.$item['tipo_participante'].'</span>',
			$item['nr_extrato'],
			$item['dt_base_extrato'],
			$item['tl_participante'].' / '.$item['qt_participante'],
			'<span class="label label-warning">'.$item['dt_liberacao_informatica'].'</span>',
			array('<span class="label label-warning">'.$item['usuario_informatica'].'</span>', 'text-align:left'),
			'<span class="label label-info">'.$item['dt_liberacao_atuarial'].'</span>',
			array('<span class="label label-info">'.$item['usuario_atuarial'].'</span>', 'text-align:left'),
			'<span class="label label-success">'.$item['dt_liberacao_comunicacao'].'</span>',
			array('<span class="label label-success">'.$item['usuario_comunicacao'].'</span>', 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>