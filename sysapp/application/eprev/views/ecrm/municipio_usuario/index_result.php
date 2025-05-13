<?php
	$head = array( 
		'Cód.',
		'Empresa',
		'Dt. Inclusão',
		'Usuário',
		'Nome',
		'E-mail',
		'Troca Senha',
		'Interno',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_usuario'],
			array($item['ds_empresa'], 'text-align:left'),
			$item['dt_inclusao'],
			array($item['ds_usuario'], 'text-align:left'),
			array($item['ds_nome'], 'text-align:left'),
			array($item['ds_email'], 'text-align:left'),
			(trim($item['fl_troca_senha']) == 'S' ? 'Sim' : 'Não'),
			(trim($item['fl_interno']) == 'S' ? 'Sim' : 'Não'),
			anchor(site_url('ecrm/municipio_usuario/cadastro/'.$item['cd_usuario']), '[editar]'),
			
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>