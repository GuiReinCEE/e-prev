<?php

#echo "<PRE>".print_r($ar_sexo,true)."</PRE>";exit;

$body=array();
$head = array( 
	'Sexo',  
	'Ativos',
	'Assistidos',
	'Pensão',
	'Total',
	'Pensionistas'
);

$sexo = "M";
$body[] = array(
		array("MASCULINO",'text-align:left;'),
	    array($ar_sexo['AT'][$sexo],'text-align:center;','int'),
	    array($ar_sexo['AS'][$sexo],'text-align:center;','int'),
	    array($ar_sexo['PA'][$sexo],'text-align:center;','int'),
	    array(($ar_sexo['AT'][$sexo] + $ar_sexo['AS'][$sexo] + $ar_sexo['PA'][$sexo]),'text-align:center;','int'),
		array($ar_sexo['PE'][$sexo],'text-align:center;','int')
	);
$sexo = "F";
$body[] = array(
		array("FEMININO",'text-align:left;'),		
	    array($ar_sexo['AT'][$sexo],'text-align:center;','int'),
	    array($ar_sexo['AS'][$sexo],'text-align:center;','int'),
	    array($ar_sexo['PA'][$sexo],'text-align:center;','int'),
	    array(($ar_sexo['AT'][$sexo] + $ar_sexo['AS'][$sexo] + $ar_sexo['PA'][$sexo]),'text-align:center;','int'),
		array($ar_sexo['PE'][$sexo],'text-align:center;','int')
	);

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->id_tabela  = 'tabela_sexo';
$grid->head       = $head;
$grid->body       = $body;
echo $grid->render();
?>