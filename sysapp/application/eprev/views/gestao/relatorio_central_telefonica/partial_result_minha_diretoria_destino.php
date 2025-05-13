<?php
$body=array();
$head = array( 
    'Posição',
	'Destino',
	$tipo
);

$nr_conta = 1;
foreach( $collection as $item )
{
	$item[$coluna] = ($coluna == "vl_ligacao" ? number_format($item[$coluna], 2, ',', '.') : $item[$coluna]);
	$body[] = array(
					$nr_conta,
					$item["destino"],
					$item[$coluna]
	               );
	$nr_conta++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();


?>
