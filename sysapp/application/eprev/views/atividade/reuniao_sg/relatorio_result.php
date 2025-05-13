<?php
$body=array();
$head = array( 
    'Dt Reuniгo',
	'Dt Inнcio Reuniгo',
    'Instituiзгo',
    'Parecer',
    'Qualificaзгo'
);
 
foreach( $collection as $item )
{
    $parecer = '';
    
    switch ($item["parecer_qualificacao"]) {
            case "P":
                $parecer = array("Positivo","text-align:center; font-weight:bold; color:blue;");
                break;
            case "N":
                $parecer = array("Negativo","text-align:center; font-weight:bold; color:red;");
                break;
            case "R":
                $parecer = array("Neutro","text-align:center; font-weight:bold; color:green;");
                break;
    }
    
    $body[] = array(
      $item['dt_reuniao'],
	  $item['dt_reuniao_ini'],
      array($item['ds_reuniao_sg_instituicao'],'text-align:left'),
      array($item['parecer'],'text-align:justify'),
      $parecer
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>