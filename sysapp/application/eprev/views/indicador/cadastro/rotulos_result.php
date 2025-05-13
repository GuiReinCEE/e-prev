<?php
$body = array();
$head = array( 
	'Nr Rótulo',
	'Descrição',
	'Coluna Tabela',
	'Último Valor',
	'Integração SA',
	'Modelo SA',
	'Tipo SA',
	''
);

foreach( $collection as $item )
{
	$editar = '<a href="javascript:void(0)" onclick="editar('.$item['cd_indicador_label'].')">[editar]</a>';
	$excluir = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_indicador_label'].')">[excluir]</a>';
	$body[] = array(
		$item['id_label'],
		array($item['ds_label'],'text-align:left;'),
		array($item['ds_coluna_tabela'],'text-align:left;'),
		$item['ultimo_valor'],
		array($item['ds_integracao_sa'],'text-align:left;'),
		array($item['ds_modelo_sa'],'text-align:left;'),
		array($item['ds_tipo_sa'],'text-align:left;'),
		$editar.' '.$excluir
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>