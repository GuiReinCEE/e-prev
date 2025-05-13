<?php
	$head = array(
		'Código',
		'De',
		'Assunto', 
		'Dt. Envio',
		'Dt. Email Enviado',
		'Usuário Envio',
		'Visualizado',
		'Para',
		'CC',
		'CCO'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor("ecrm/reenvio_email/index/".$item["cd_email"], $item["cd_email"], "target='_blank'"),
			$item['de'],
			array($item['assunto'], 'text-align:left;'),
			$item['dt_envio'],
			$item['dt_email_enviado'],
			array($item['nome'], 'text-align:left;'),
			($item['fl_visualizado'] == 'S' ? '<span class="label label-warning">Sim</span>' : '<span class="label">Não</span>'),
			array($item['para'], 'text-align:left;'),
			array($item['cc'], 'text-align:left;'),
			array($item['cco'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>