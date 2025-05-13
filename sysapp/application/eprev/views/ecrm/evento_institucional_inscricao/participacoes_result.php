<?php
$body=array();
$head = array('Dt Evento', 'Evento', 'Presente');
foreach( $collection as $item )
{
    $body[] = array(
      $item['dt_inicio'],
      array($item['nome'], 'text-align:left'),
      (trim($item['fl_presente']) == 'S' ? '<span class="label label-success">Sim</span>' : '<span class="label">Não</span>')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>