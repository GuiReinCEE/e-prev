<?php
$body=array();
$head = array( 
	'Programa', 'Ger�ncia','Defini��o','Data de cadastro'
);

foreach( $collection as $item )
{
	$link=anchor( "cadastro/software/detalhe/".md5($item["programa"]), $item["programa"]);

	$body[] = array(
		 array($link, "text-align:left;")
		, $link_telas
		, $item["cd_divisao"]
		, array($item["definicao"], "text-align:left;")
		, $item["dt_cadastro"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>