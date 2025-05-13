<?php

$body = array();
$head = array(
  'Tipo',
  'Empresa',
  'Nome',
  'Cargo',
  'Telefone',
  'Email',
  'Secretária',
  'Sec. Telefone',
  'Sec. Email'
);

foreach ($collection as $item)
{
    $body[] = array(
      array($item["ds_contato_institucional_tipo"],'text-align:left'),
      array($item["ds_contato_institucional_empresa"],'text-align:left'),
      array(anchor("ecrm/contato_institucional/cadastro/".$item["cd_contato_institucional"], $item["nome"]),'text-align:left'),
      array($item["ds_contato_institucional_cargo"],'text-align:left'),
      $item["telefone_1"].br().$item["telefone_2"],
      $item["email_1"].br().$item["email_2"],
      array($item["sec_nome"],'text-align:left'),
      $item["sec_telefone_1"].br().$item["sec_telefone_2"],
      $item["sec_email_1"].br().$item["sec_email_2"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>