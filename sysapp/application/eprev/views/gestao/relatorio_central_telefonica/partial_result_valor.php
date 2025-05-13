<img src="<?=$image['name']?>" border="0">
<BR>
<?php
$body=array();
$head = array( 
    'Posição',
	'Ramal',
	'Nome',
	'Conta',
	'Valor (R$)'
);

$nr_conta = 1;
foreach( $collection as $item )
{
	$body[] = array(
	$nr_conta,
	$item["ramal"],
	array($item["nome"],"text-align:left;"),
	$item["conta"],
	number_format($item['vl_ligacao'], 2, ',', '.')
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