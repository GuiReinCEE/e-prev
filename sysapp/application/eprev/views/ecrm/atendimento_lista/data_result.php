<?php
$body=array();
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();

$head = array(
	'Data', 'Atendimentos'
);

foreach($collection as $item)
{
    #$data= str_replace('/','',$item["dt_data"]);

	$body[] = array(
        #anchor("ecrm/atendimento_lista/index/0/0/" . $data, $item["dt_data"]),
        array($item["dt_data"],'text-align:left;'),
		array($item["qt_atendimento"],'text-align:right;','int')
	);

    $ar_titulo[] = $item['dt_data'];
	$ar_dado[] = $item['qt_atendimento'];
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