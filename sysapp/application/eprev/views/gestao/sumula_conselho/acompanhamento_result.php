<?php
$body=array();
$head = array(
    'Dt Acompanhamento',
    'Descrição',
	'Item',
    'Usuário',
);

foreach($collection as $item )
{
    $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_sumula_conselho_acompanhamento'].')">[excluir]</a>';
    
    $body[] = array(
      $item["dt_inclusao"],
      array($item["descricao"], "text-align:justify;"),
      array($item["item"], "text-align:left;"),
      $item['nome']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();