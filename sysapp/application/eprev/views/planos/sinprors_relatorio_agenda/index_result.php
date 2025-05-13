<?php
$body=array();
$head = array( 
	'Ano/Mês', 
	'Interessados',
	'Agendamentos',
	'Inscritos',
	'Participantes'
);

$qt_interessado = 0;
$qt_inscrito = 0;
$qt_participante = 0;

foreach($ar_reg as $ar_item)
{
	$body[] = array(
	    $ar_item["ano_mes"],
		array($ar_item["qt_interessado"],'text-align:right;','int'),
		array($ar_item["qt_agenda"],'text-align:right;','int'),
		array($ar_item["qt_inscrito"],'text-align:right;','int'),
		array($ar_item["qt_participante"],'text-align:right;','int')
	);
	
	$qt_interessado+= intval($ar_item["qt_interessado"]);
	$qt_inscrito+= intval($ar_item["qt_inscrito"]);
	$qt_participante+= intval($ar_item["qt_participante"]);
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();


?>