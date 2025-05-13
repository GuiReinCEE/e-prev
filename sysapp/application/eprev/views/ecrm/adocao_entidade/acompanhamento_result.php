<?php
$body = array();
$head = array( 	
	'Dt Inclusуo',
	'Usuсrio',
	'Data',
	'Descriчуo',
	
);

foreach( $collection as $item )
{	
    $body[] = array(
		$item['dt_inclusao'],
		$item['nome'],
		$item['dt_adocao_entidade_acompanhamento'],
		array(nl2br($item['ds_adocao_entidade_acompanhamento']), 'text-align:justify')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>