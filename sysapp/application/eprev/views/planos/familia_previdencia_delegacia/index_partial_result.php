<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome',
	'Endereço',
	'Cidade',
	'UF',
	'Telefone',
	'Email'
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("planos/familia_previdencia_delegacia/cadastro/".$item["cd_delegacia"], $item["cd_delegacia"]),
		array(anchor("planos/familia_previdencia_delegacia/cadastro/".$item["cd_delegacia"],$item["nome"]),"text-align:left;"),
		array($item["endereco"],"text-align:left;"),
		array($item["cidade"],"text-align:left;"),
		array($item["uf"],"text-align:left;"),
		array($item["telefone"],"text-align:left;"),
		array($item["email"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
