<?php

$body = array();
$head = array(
  'Número',
  'Descrição',
  'Processo',
  'Cadastrado / Responsável',
  'Dt. Limite/<br>Apres. AC',
  'Dt. Proposta',
  'Dt. Prorrogação',
  'Dt. Implementação',
  'Dt. Valid. eficácia',
  'Dt. Encerramento',
  'Dt. Último Acomp.'
);

foreach ($collection as $item)
{
    $body[] = array(
      anchor("gestao/nc/cadastro/" . $item["cd_nao_conformidade"], $item["numero_cad_nc"]),
      array("<div style='width:350px;'>" . anchor("gestao/nc/cadastro/" . $item["cd_nao_conformidade"], $item["descricao"]) . "</div>", 'text-align:left;'),
      $item["procedimento"],
      $item["nome_aberto_por"] . '<br><b><i>' . $item["nome_responsavel"] . '</i></b>',
	  
	  '<span class="label">'.$item["dt_limite_apres"].'</span>'
	  .'<br>'.
	  '<span class="label '.($item["fl_apresentada_fora_prazo"] == "S" ? "label-important" : "").'">'.$item["dt_apres"].'</span>',
	  
	  
	  '<span class="label '.($item["fl_proposta_fora_prazo"] == "S" ? "label-important" : "").'">'.$item["dt_prop_imp"].'</span>',
	  '<span class="label '.($item["fl_proposta_fora_prazo"] == "S" ? "label-important" : "").'">'.$item["dt_prorrogada"].'</span>',
	  '<span class="label '.($item["fl_implementada_fora_prazo"] == "S" ? "label-important" : "").'">'.$item["dt_efe_imp"].'</span>',
      $item['dt_prop_verif'],
      $item['dt_encerramento'],
      $item['dt_acompanhamento']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>