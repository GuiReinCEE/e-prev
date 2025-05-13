<?php
$body=array();
$head = array(
    'Dt Acompanhamento',
    'Descrição',
    'Usuário',
	''
);

foreach($collection as $item )
{
	$body[] = array(
		$item["dt_inclusao"],
		array(nl2br($item["descricao"]), "text-align:justify;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_desenquadramento_cci_acompanhamento'].')">[excluir]</a>'
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();