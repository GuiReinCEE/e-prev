<?php

$body = array();
$head = array(
  'C�d Documento'
  , 'Documento'
  , 'Ver. Eletro'
  , 'Empresa'
  , 'Usu�rio'
  , 'Dt Inclus�o'
);

foreach ($collection as $item)
{
    $body[] = array(
      anchor("ecrm/certificado_participante_documento/cadastro/".$item["cd_certificado_participante_documento"], $item["cd_documento"]),
      array($item["nome_documento"], 'text-align:left'),
      (trim($item['fl_verificar']) == 'S' ? 'Sim' : 'N�o'),
      array($item["sigla"], 'text-align:left'),
      array($item["nome"], 'text-align:left'),
      $item["dt_inclusao"],
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>