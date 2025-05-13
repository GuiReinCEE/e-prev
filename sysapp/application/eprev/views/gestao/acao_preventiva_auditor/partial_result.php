<?php

$body = array();
$head = array(
  'Processo',
  'Vigente',
  '1К opчуo',
  '2К opчуo'
);

foreach ($collection as $item)
{
    $body[] = array(
      array(anchor("gestao/acao_preventiva_auditor/cadastro/".$item["cd_processo"], $item["procedimento"]),'text-align:left'),
      $item["fl_vigente"],
	  array($item["usuario_titular"],'text-align:left'),
      array($item["usuario_substituto"],'text-align:left')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>