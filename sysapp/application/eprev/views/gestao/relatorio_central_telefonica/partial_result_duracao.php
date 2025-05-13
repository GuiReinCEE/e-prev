<img src="<?=$image['name']?>" border="0">
<BR>
<?php
$body=array();
$head = array( 
	'Posição',
	'Ramal',
	'Nome',
	'Conta',
	'Duração'
);

$nr_conta = 1;
foreach( $collection as $item )
{
	$body[] = array(
	$nr_conta,
	$item["ramal"],
	array($item["nome"],"text-align:left;"),
	$item["conta"],
	$item['hr_ligacao']
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
