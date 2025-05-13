<?php
$body = array();
$head = array( 
	"Ano",
	""
);

foreach( $collection as $item )
{
	$body[] = array(
		anchor("gestao/caderno_cci/estrutura/".$item["cd_caderno_cci"], $item["nr_ano"]),
		($fl_excluir ? '<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci"].')">[excluir]</a>' : "").' '.
		(trim($item['nr_mes']) != '' ? '<a href="javascript:void(0);" onclick="apresentacao('.$item["cd_caderno_cci"].', '.(trim($item["nr_mes"]) == '' ? '0' : '1').', '.$item["nr_mes"].')">[apresentação - '.mes_format(trim($item['nr_mes']),'mmmm').']</a>': '').br().'<a href="javascript:void(0);" onclick="csv('.$item["cd_caderno_cci"].')">[csv]<a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>