<?php
$body = array();
$head = array(
	'#',
	'Usuário',
	'Data',
	'Status',
	'Complemento'
);

$num=0;
foreach($collection as $item)
{
	$num++;
	$body[] = array(
				$num, 
				array($item['responsavel'],"text-align:left;"), 
				$item['data'], 
				'<span class="'.trim($item['class_status']).'">'.trim($item['status']).'</span>',
				array(nl2br($item['complemento']),"text-align:justify;")
			);
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>