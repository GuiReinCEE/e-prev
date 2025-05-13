<?php
$body=array();
$ar_titulo = Array();
$ar_dado = Array();
$ar_image = Array();

$head = array(
	'Usuário', 'Atendimentos'
);

foreach($collection as $item)
{
	$body[] = array(
        array(anchor("ecrm/atendimento_lista/index/0/0/0/" . $item["id_atendente"], $item["nome"]),'text-align:left;'),
		array($item["qt_atendimento"],'text-align:right;','int')
	);

    $ar_titulo[] = $item['nome'];
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