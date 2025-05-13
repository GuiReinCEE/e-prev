<?php
	$head = array(
		'Assunto',
		'Dt. Inclusão',
		''
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		$body[] = array(
			array(anchor('ecrm/reclamacao_assunto/cadastro/'.$item['cd_reclamacao_assunto'], $item['ds_reclamacao_assunto']), 'text-align:left;'),
			$item['dt_inclusao'],
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_reclamacao_assunto'].');">[excluir]</a>'
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>