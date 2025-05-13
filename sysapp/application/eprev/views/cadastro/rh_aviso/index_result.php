<?php
	$head = array(
		'C�d',
		'Descri��o',
		'Periodicidade',
		'Dt Refer�ncia',
		'Dia',
		'Dt Inclus�o',
		'Usu�rio',
		'Usu�rio Conf.',
		'Dt. Confirma��o',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url('cadastro/rh_aviso/historico/'.$item['cd_rh_aviso']), $item['cd_rh_aviso']),
			array(anchor(site_url('cadastro/rh_aviso/historico/'.$item['cd_rh_aviso']), $item['ds_descricao']), 'text-align:left;'),
			'<span class="label '.$item['ds_class_periodicidade'].'">'.$item['ds_periodicidade'].'</span>',
			$item['dt_referencia'],
			$item['ds_dia'],
			$item['dt_inclusao'],
			array($item['ds_usuario'], 'text-align:left;'),
			array($item['ds_usuario_conferencia'], 'text-align:left;'),
			$item['dt_confirmacao'],
			(trim($item['fl_confirmar']) == 'S' ? '<a href="javascript: confirmar('.$item['cd_rh_aviso'].')">[confirmar]</a> ' : '').
			'<a href="javascript: excluir_item('.$item['cd_rh_aviso'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>