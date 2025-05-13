<?php

$body = array();
$head = array(
  'Cód.',
  'Arquivo',
  'Empresa',
  'Dt Inclusão',
  'Usuário',
  ''
);

foreach ($collection as $item)
{

    $body[] = array(
      $item["cd_formulario"],
      array(anchor("../../eletroceee/extranet/up/formulario/".$item["arquivo"], $item["arquivo_nome"], array('target' => '_blank')), 'text-align:left'),
      $item["sigla"],
      array($item["nome"], 'text-align:left'),
      $item["dt_inclusao"],
      '<a href="javascript:void(0)" onclick="excluir('.$item['cd_formulario'].');">[excluir]</a>'
    );
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>