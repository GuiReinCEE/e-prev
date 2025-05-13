<?php
$body = Array();
$head = array(
	'Código', 
	'Documento', 
	''
);

foreach ($collection as $item)
{
	$body[] = array(
	  $item['cd_documento'],
	  array($item['nome_documento'], 'text-align:left;'),
	  '<a href="javascript:void(0);" onclick="seleciona_documento('.$item["cd_documento"].')">Adicionar</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
$grid->view_count = false;
echo $grid->render();
?>