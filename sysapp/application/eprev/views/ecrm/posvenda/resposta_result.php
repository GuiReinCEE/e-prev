<?php
$body = array();
$head = array( 
	'',
	'Data',
	'Acompamento',
	'Usuário'
);

foreach( $collection as $item )
{
	$body[] = array(
        $item['cd_pos_venda_participante_acompanhamento'],
        $item["dt_inclusao"],
        array($item["acompanhamento"],"text-align:justify;"),
        array($item["nome"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>