<?php

$body = array();
$head = array(
  'Tabela',
  'T - W',
  'Inicio',
  'Final',
  'Tempo',
  'Atualizados',
  'Origem',
  'Atual',
  'Tipo',
  'BD',
  'Categoria'
);

foreach ($collection as $item)
{
    $style = 'text-align:center; ';
    if($item['num_registros'] != $item['qt_total_registro'])
    {
        $style .= 'font-weight:bold;';
    }
    if($item['num_registros'] < $item['qt_total_registro'])
    {
        $style .= 'color:red; font-weight:bold;';
    }
    
    $body[] = array(
      array(anchor("servico/tabelas_atualizar/cadastro/".$item["tabela"], $item["tabela"]),'text-align:left'),
      $item['truncar'] .' - '.$item['condicao'],
      $item['dt_inicio'],
      $item['dt_final'],
      $item['hr_tempo'],
      $item['num_registros_atualizados'],
      $item['num_registros'],
       array($item['qt_total_registro'],$style),
      $item['periodicidade'],
      $item['bd_origem'],
      $item['categoria_importa']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
	 	 	 	 	 	 	 	 	
?>