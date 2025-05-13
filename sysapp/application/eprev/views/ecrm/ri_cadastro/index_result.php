<?php
$body=array();
$head = array( 
	'#',
	'Nome',
	'Empresa',
	'Cidade',
	'UF',
	'País',
	'Telefone',
	'Celular',
	'Email',
	'Origem'
);

foreach( $collection as $item )
{
	$body[] = array(
	anchor( 'ecrm/ri_cadastro/cadastro/'.$item['cd_cadastro'], $item['cd_cadastro']),
	array(anchor( 'ecrm/ri_cadastro/cadastro/'.$item['cd_cadastro'], $item["nome"]),"text-align:left;"),
	array($item["empresa"],"text-align:left;"),
	array($item["cidade"],"text-align:left;"),
	array($item["uf"],"text-align:left;"),
	array($item["pais"],"text-align:left;"),
	array("(".$item["telefone_ddd"].")".$item["telefone"],"text-align:left;"),
	array("(".$item["celular_ddd"].")".$item["celular"],"text-align:left;"),
	$item["email"],
	$item["origem"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
