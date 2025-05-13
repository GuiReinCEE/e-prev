<?php
$body=array();
$head = array( 
	'Ano/Mês',
	'Convidado',
	'Estimado',
	'Presente',
	'Respondente',
	'Satisfeito'
);

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		array($ar_item["nr_mes"],'font-weight: bold;'),

		array(number_format(intval($ar_item["nr_convidado"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_estimado"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_presente"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),

		array(number_format(intval($ar_item["nr_respondente"]),0,",","."),'text-align:right; color: green; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_satisfeito"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
