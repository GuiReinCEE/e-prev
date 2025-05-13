<?php
$body = array();
$head = array('Cód','Arquivo');
$nr_id = 1;
foreach ($ar_reg as $item)
{
    $body[] = array(
		$nr_id,
		array(anchor("servico/log_postgresql/detalhe/".$item["arquivo"], $item["arquivo"]),'text-align:left'),
		#$item['arquivo']
    );
	$nr_id++;
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>