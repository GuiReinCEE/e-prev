<?php
	$head = array(
		'Nъmero',
		'Pauta',
		'Sъmula',
		'Data',
		'Dt Divulgaзгo'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$link = '';

		if(gerencia_in(array('SG')))
    	{
    		$link = ' '.anchor('gestao/sumula_interventor_inicial/cadastro/'.$item['cd_sumula_interventor'], '[editar]');
    	}

		$body[] = array(
			$item['nr_sumula_interventor'].$link,
			array($item['arquivo_pauta_nome'].' '.anchor(site_url('gestao/sumula_interventor_inicial/abrir_pdf/'.$item['cd_sumula_interventor'].'/P'), '[ver arquivo]', array('target' => '_blank')), 'text-align:left;'),
			array($item['arquivo_sumula_nome'].' '.anchor(site_url('gestao/sumula_interventor_inicial/abrir_pdf/'.$item['cd_sumula_interventor'].'/S'), '[ver arquivo]', array('target' => '_blank')), 'text-align:left;'),
			$item['dt_sumula_interventor'],
			$item['dt_divulgacao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>