<?php
	$head = array(
		'Gerência',
		'',
		'Nome',
		'Dt Admissão',
		'Cargo / Área de Atuação',
		'Classe',
		'Dt. Progressão/Promoção'
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
			anchor('cadastro/rh_progressao_promocao/cadastro/'.$item['cd_usuario'], $item['ds_gerencia']),
			'<a href="'.site_url('cadastro/avatar/index/'.intval($item['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img width="50" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>',
			array(anchor('cadastro/rh_progressao_promocao/cadastro/'.$item['cd_usuario'], $item['ds_nome']), 'text-align:left;'),
			$item['dt_admissao'],
			array($item['ds_cargo_area_atuacao'], 'text-align:left'),
			$item['ds_classe'],
			$item['dt_progressao_promocao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();