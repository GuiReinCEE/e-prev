<?php
	$head = array(
		'Dt. Reunião',
		'Tipo',
		'Processos',
		'Dt. Atualização Apresentação',
		'Dt. Encerramento',
		'Usuário Encerramento',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/reuniao_sistema_gestao/cadastro/'.$item['cd_reuniao_sistema_gestao'], $item['dt_reuniao_sistema_gestao']),
			array(anchor('gestao/reuniao_sistema_gestao/cadastro/'.$item['cd_reuniao_sistema_gestao'], $item['ds_reuniao_sistema_gestao_tipo']), 'text-align:left'),
			array((count($item['processo']) > 0 ? implode(br(), $item['processo']) : ''), 'text-align:justify'),
			$item['dt_apresentacao'],
			$item['dt_encerramento'],
			$item['usuario_encerramento'],
			($item['dt_apresentacao'] != '' ? '<a href="'.site_url('gestao/reuniao_sistema_gestao/apresentacao/'.$item['cd_reuniao_sistema_gestao']).'" target="_blank">[apresentação]</a>' : '').' '.
			($item['arquivo'] != '' ? anchor(base_url().'up/reuniao_sistema_gestao/'.$item['arquivo'], '[ata]' , array('target' => "_blank")) : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>