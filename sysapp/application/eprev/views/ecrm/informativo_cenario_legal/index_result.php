<?php
	$head = array( 
		'Edição', 
		'Título', 
		'Dt. Inclusão',
		'Cenário',
		'Dt. Envio',
		'Conteúdo'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor(site_url('ecrm/informativo_cenario_legal/cadastro/'.$item['cd_edicao']), $item['cd_edicao']), 'text-align:left;'),
			array(anchor(site_url('ecrm/informativo_cenario_legal/cadastro/'.$item['cd_edicao']), $item['tit_capa']), 'text-align:left;'),
			$item['dt_edicao'],
			array(implode(br(), $item['conteudo']), 'text-align:justify;'),
			$item['dt_envio_email'],
			array(anchor(site_url('ecrm/informativo_cenario_legal/conteudo/'.$item['cd_edicao']), '[editar]'), 'text-align:center;')

		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>