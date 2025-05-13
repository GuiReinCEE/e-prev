<?php

#echo "<PRE>".print_r($ar_plano,true)."</PRE>";exit;

$body=array();
$head = array( 
	'Plano',  
	'Ativos',
	'Assistidos',
	'Pensão',
	'Total',
	'Pensionistas'
);

foreach($ar_plano as $ar_item)
{
$body[] = array(
		array($ar_item['ds_plano'],'text-align:left;'),
	    array($ar_item['AT'],'text-align:center;','int'),
	    array($ar_item['AS'],'text-align:center;','int'),
	    array($ar_item['PA'],'text-align:center;','int'),
	    array(($ar_item['AT'] + $ar_item['AS'] + $ar_item['PA']),'text-align:center;','int'),
		array($ar_item['PE'],'text-align:center;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->id_tabela  = 'tabela_plano';
$grid->head       = $head;
$grid->body       = $body;
echo $grid->render();
?>