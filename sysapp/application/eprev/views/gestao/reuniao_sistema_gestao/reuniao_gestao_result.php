<?php
	$head = array(
		'Dt. Reunião',
		'Tipo',
		'Processos',
		'Dt. Encerramento',
		'Anexo',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$link = '';

		foreach ($item['anexo'] as $key => $item1) 
		{
			$link .= '<label style"text-align:left;">'.anchor(base_url().'up/reuniao_sistema_gestao/'.$item1['arquivo'], $item1['arquivo_nome'], array('target' => "_blank")).'<br>'.'</label>';
		}

		$body[] = array(
			$item['dt_reuniao_sistema_gestao'],
			array(($item['ds_reuniao_sistema_gestao_tipo']), 'text-align:left'),
			array((count($item['processo']) > 0 ? implode(br(), $item['processo']) : ''), 'text-align:justify'),
			$item['dt_encerramento'],
			array(($link), 'text-align:left'),
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