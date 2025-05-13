<?php
$body = array();
$head = array( 
	'Cód.',
	'Entidade',
	'CNPJ',
	'Código de Recolhimento',
	'Telefone 1',
	'Telefone 2',
	''
);

foreach( $collection as $item )
{
	$opcao = '';
	
	if(trim($item['dt_exclusao']) == '')
	{
		$opcao = '<a href="javascript:void(0)" onclick="desativar('.intval($item['cd_entidade']).')">[desativar]</a>';
	}
	else
	{
		$opcao = '<a href="javascript:void(0)" onclick="ativar('.intval($item['cd_entidade']).')">[ativar]</a>';
	}

	$body[] = array(
		anchor("atividade/entidade/cadastro/".$item["cd_entidade"], $item["cd_entidade"]),
		array(anchor("atividade/entidade/cadastro/".$item["cd_entidade"], $item["ds_entidade"]), 'text-align:left;'),
		$item['cnpj'],
		implode(", ", $item["recolhimento"]),
		$item['telefone1'],
		$item['telefone2'],
		$opcao
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>