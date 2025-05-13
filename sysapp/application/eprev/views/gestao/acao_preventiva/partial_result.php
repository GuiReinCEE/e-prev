<?php

$body = array();
$head = array(
  'N�mero',
  'Ano',
  'Processo',
  'Potencial NC',
  'Ger�ncia',
  'Respons�vel',
  'Auditores',
  'Dt Cadastro',
  'Dt Proposta',
  'Dt Prorroga��o',
  'Dt Implementa��o',
  'Dt Valida��o Efic�cia',
  'Dt Verifica��o da Efic�cia',
  'Dt Cancelado'
);

foreach ($collection as $item)
{
    $body[] = array(
      anchor("gestao/acao_preventiva/cadastro/" . $item["nr_ano"] . '/' . $item["nr_ap"], $item["numero_cad_ap"]),
      $item["nr_ano"],
      array($item["procedimento"], 'style="text-align:left;"'),
      array("<div style='width:350px;'>" . anchor("gestao/acao_preventiva/cadastro/" . $item["nr_ano"] . '/' . $item["nr_ap"], $item["potencial_nc"]) . "</div>", 'text-align:left;'),
      $item["cd_divisao"],
      array($item["nome"], 'style="text-align:left;"'),
      $item["auditor"] . '<br><i>' . $item["substituto"] . '</i>',
      $item["dt_inclusao"],
      $item["dt_proposta"],
      $item["dt_prorrogacao"],
      $item["dt_implementacao"],
      $item["dt_prazo_validacao"],
      $item["dt_validacao"],
      $item["dt_cancelado"]
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>