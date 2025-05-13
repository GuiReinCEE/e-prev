<?php
$head = array( 
	'Descriчуo',
	'Tipo Controle',
	'Dt. De Expiraчуo',
	'Link'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		array(nl2br(anchor('servico/dominio/renovacao/'.$item['cd_dominio'],$item['descricao'])),'text-align:left;'),
		array($item['ds_dominio_tipo'],'text-align:left;'),
		anchor('servico/dominio/renovacao/'.$item['cd_dominio'],$item['dt_dominio_renovacao']),
		array(nl2br( $item['ds_dominio']),'text-align:left;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>