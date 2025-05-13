<?php
	$head = array( 
		'Código',
		'',
		'Usuário',
		'Nome',
		'Gerência',
		'Ident. Usual',
		'Tipo',
		'Cargo / Área de Atuação', 
		'RE', 
		'Celular', 
		'Dt Admissão'
	);

	$body = array();

	foreach($collection as $item)
	{
		$avatar_arquivo = trim($item['avatar']);
		
		if(trim($avatar_arquivo) == '')
		{
			$avatar_arquivo = $item['usuario'].'.png';
		}
		
		if(!file_exists('./up/avatar/'.$avatar_arquivo))
		{
			$avatar_arquivo = 'user.png';
		}	
		
		$body[] = array(
			anchor('cadastro/rh/cadastro/'.$item['codigo'], $item['codigo']),
			'<a href="'.site_url('cadastro/avatar/index/'.intval($item['codigo'])).'" title="Clique aqui para ajustar a foto"><img width="50" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>',
			array($item['usuario'], 'text-align:left;'),
			array(anchor('cadastro/rh/cadastro/'.$item['codigo'], $item['nome']), 'text-align:left;'),
			$item['divisao'],
			array($item['guerra'], 'text-align:left;'),
			$item['papel'],
			array((trim($item['ds_cargo_area_atuacao']) != '' ? $item['ds_cargo_area_atuacao'] : $item['nome_cargo']), 'text-align:left;'),
			$item['cd_registro_empregado'],
			$item['celular'],
			$item['dt_admissao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();