<?php
$body=array();
$head = array( 
    'Gerência',
    'Usuário',
    ''
);

foreach( $collection as $item )
{

	$excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_reuniao_sg_permissao'].')">[excluir]</a>';

    $body[] = array(
		array($item["divisao"],"text-align:left;"),
		array($item["nome"],"text-align:left;"),
		$excluir
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>