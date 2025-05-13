<?php
	$head = array(
		'Código',
		'Justificativa',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/pauta_sg_justificativa/cadastro/'.$item['cd_pauta_sg_justificativa'], $item['cd_pauta_sg_justificativa']),
			array(anchor('gestao/pauta_sg_justificativa/cadastro/'.$item['cd_pauta_sg_justificativa'], $item['ds_pauta_sg_justificativa']), 'text-align:left'),
			'<a href="javascript:void(0)" onclick="excluir('.intval($item['cd_pauta_sg_justificativa']).')">[excluir]</a>'
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>