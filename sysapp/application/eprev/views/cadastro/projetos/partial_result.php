<?php

$body = array();
$head = array(
  'Projeto', 'Сrea', 'Descriчуo', 'Data de cadastro'
);

foreach ($collection as $item)
{

    $body[] = array(
      array(anchor("cadastro/projeto/detalhe/".$item["codigo"], $item["nome"]), 'text-align:left;'), 
      $item["area"], 
      array($item["descricao"], 'text-align:left;'), 
      $item["data_cad"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>