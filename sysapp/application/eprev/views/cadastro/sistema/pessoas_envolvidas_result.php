<?php

$body = array();
$head = array(
  'Nome', ''
);

foreach ($collection as $item)
{

    $body[] = array(
      array($item["nome"], 'text-align:left;'), 
      '<a href="javascript:void(0)" onclick="excluir_envolvido('.$item['cd_envolvido'].')">[excluir]</a>'
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>