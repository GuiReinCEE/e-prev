<img src="<?=$image['name']?>" border="0">
<BR>
<?php
$body=array();
$head = array( 
    'Posição',
	'Ramal',
	'Nome',
	$tipo
);

$nr_conta = 1;
foreach( $collection as $item )
{
	$item[$coluna] = ($coluna == "vl_ligacao" ? number_format($item[$coluna], 2, ',', '.') : $item[$coluna]);
	$body[] = array(
	$nr_conta,
	$item["ramal"],
	array($item["nome"],"text-align:left;"),
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
