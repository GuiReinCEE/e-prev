<?php
$body = array();
$head = array(
  'Ano/Número',
  'Entidade',
  'Mês/Ano Ref',
  'Dt. Envio',
  'Dt. Recebido',
  'Dt Retorno'
);

foreach ($collection as $item)
{            
  $body[] = array(
    anchor('atividade/entidade_movimento/receber/'.$item['cd_movimento'], $item['nr_ano_numero']),
    array($item["ds_entidade"], "text-align:left;"),
    $item['dt_referencia'],
    $item['dt_envio'],
    $item['dt_recebido'],
    $item['dt_retorno']
  );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>