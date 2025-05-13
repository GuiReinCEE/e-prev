<?php
$body = array();
$head = array(
	'Evento',
	'Responsável',
	'Data',
	'Status',
	'Descrição',
	'Motivo'
);

$num=0;
foreach($collection as $item)
{
	$num++;
	$body[] = array(
			$num, 
			array($item['responsavel'],"text-align:left;"), 
			$item['data'], 
			'<span style="font-weight:bold; color:'.$item["status_cor"].';">'.$item["status_atual"].'</span>',
			array($item['descricao'],"text-align:left;"), 
			array(nl2br($item['motivo']),"text-align:left;")
		);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>