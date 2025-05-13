<?php
	$head = array(
		'Cуd',
		'Descriзгo',
		'Responsбveis',
	   	'Dt. Prazo',
	   	'Dt. Verificado',
	   	'Usuбrio Verificado'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor(site_url('gestao/gestao_aviso/cadastro/'.$item['cd_gestao_aviso']), $item['cd_gestao_aviso']),
			array(anchor(site_url('gestao/gestao_aviso/cadastro/'.$item['cd_gestao_aviso']), $item['ds_descricao']), 'text-align:left;'),
			array(implode(br(),$item['usuario']), 'text-align:left;'),
			$item['dt_referencia'],
			$item['dt_verificacao'],
			array($item['ds_usuario_verificado'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>