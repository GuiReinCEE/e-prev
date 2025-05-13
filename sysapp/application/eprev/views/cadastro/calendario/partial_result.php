<?php

$body = array();
$head = array(
  'Data', 'Descriчуo', 'Tipo', 'Turno'
);

foreach ($collection as $item)
{

    $body[] = array(
      anchor("cadastro/calendario/cadastro/".$item["cd_calendario"], $item["dt_calendario"]), 
      array(anchor("cadastro/calendario/cadastro/".$item["cd_calendario"], $item["descricao"]), 'text-align:left;'), 
      array($item["tp_calendario"], 'text-align:left;'),  
      array($item["turno"], 'text-align:left;'), 
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>