<?php
	$head = array( 
		'Dt Inclusão',
		'Arquivo',
		'Usuário',
		''
	);

	$body = array();

	foreach( $collection as $item )
	{	
	    $body[] = array(
			$item['dt_inclusao'],
			array(anchor(base_url().'up/reuniao_sistema_gestao/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), 'text-align:left;'),
			$item['nome'],
			'<a href="javascript:void(0);"" onclick="excluir('.$item['cd_reuniao_sistema_gestao_anexo'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>