<?php
$body=array();
$head = array( 
	'Ano/Mês',
	'Informativos',
	'Informativos fora<BR>do prazo',
	'Informativos com<BR>retrabalho',
	'Exemplares',
	'Público',
	'Retrabalho',
	'Reclamações'
);

foreach($ar_reg as $ar_item )
{
	$body[] = array(
		array($ar_item["nr_mes"],'font-weight: bold;'),

		array(number_format(intval($ar_item["qt_informativo"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_atrasado"]),0,",","."),'text-align:right; color: red; font-weight: bold;','int'),
		array(number_format(intval($ar_item["qt_retrabalho"]),0,",","."),'text-align:right; color: OrangeRed; font-weight: bold;','int'),
		
		array(number_format(intval($ar_item["nr_exemplar"]),0,",","."),'text-align:right; color: blue; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_publico"]),0,",","."),'text-align:right; color: gray; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_retrabalho"]),0,",","."),'text-align:right; color: OrangeRed; font-weight: bold;','int'),
		array(number_format(intval($ar_item["nr_reclamacao"]),0,",","."),'text-align:right; color: red; font-weight: bold;','int')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(5);
?>
