<?php
$body=array();
$head = array( 
	'Ano/M�s',
	'Qt Pessoas',
	'Qt Envio',
	'Qt Visualizaram',
	'Qt Visualiza��es'
);


foreach($collection as $item )
{
	$body[] = array(
		$item['mes'],
		$item['qt_pessoa'],
		$item['qt_envio'],
		$item['qt_pessoa_acesso'],
		$item['qt_acesso']
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
