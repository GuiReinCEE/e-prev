<?php
	$head = array(
		'Código',
		'Objetivo',
		'Anexo obrigatório',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$body[] = array(
			anchor('gestao/pauta_sg_objetivo/cadastro/'.$item['cd_pauta_sg_objetivo'], $item['cd_pauta_sg_objetivo']),
		    array(anchor('gestao/pauta_sg_objetivo/cadastro/'.$item['cd_pauta_sg_objetivo'],$item['ds_pauta_sg_objetivo']), 'text-align:left;'),
		    $item['ds_anexo_obrigatorio'],
			'<a href="javascript:void(0)" onclick="excluir('.intval($item['cd_pauta_sg_objetivo']).')">[excluir]</a>'
 		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>