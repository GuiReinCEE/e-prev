<?php
$body=array();
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();

$head = array(
	'Programa ', 'Atendimentos', 'Tempo'
);

foreach($collection as $item)
{

	$body[] = array(
        array($item["tp_programa"],'text-align:left;'),
        $item["qt_tempo"],
		array($item["qt_programa"],'text-align:right;','int')
	);

    $ar_titulo[] = $item['tp_programa'];
	$ar_dado[] = $item['qt_programa'];
}


$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
echo br(2);

if(count($ar_dado) != 0)
{
    $ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Atendimentos');
    echo '<center><img src="'.$ar_image['name'].'" border="0"></center>';
}

?>