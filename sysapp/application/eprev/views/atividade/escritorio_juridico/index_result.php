<?php
$body=array();
$head = array( 
	'Cód.',
	'Nome Fantasia',
	'Representante',
	'CGC',
	''
);

foreach( $collection as $item )
{
	$opcao = '';
	
	if(trim($item['cd_escritorio']) == '')
	{
		$opcao = '<a href="javascript:void(0);" onclick="ativar('.intval($item['cd_escritorio_oracle']).')">[ativar]</a>';
	}
	else
	{
		if(trim($item['dt_exclusao']) == '')
		{
			$opcao = '<a href="javascript:void(0);" onclick="desativar('.intval($item['cd_escritorio']).')">[desativar]</a>';
		}
		else
		{
			$opcao = '<a href="javascript:void(0);" onclick="reativar('.intval($item['cd_escritorio']).')">[reativar]</a>';
		}
	}

	$body[] = array(
		$item['cd_escritorio_oracle'],
		array($item['nome_fantasia'], 'text-align:left;'),
		array($item['representante'], 'text-align:left;'),
		$item['cgc'],
		$opcao
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>