<?php
#echo "<PRE>".print_r($ar_categoria,true)."</PRE>";exit;

	$body=array();
	$head = array( 
		'Faixa',  
		'Ativos',
		'Assistidos',
		'Pensão',
		'Total',
		'Pensionistas'
	);

	
	$nr_conta_idade = 0;
	foreach($ar_categoria as $ar_idade)
	{
		$body[] = array(
			array($ar_idade['desc'],'text-align:left;'),
			array($ar_idade['AT'],'text-align:center;','int'),
			array($ar_idade['AS'],'text-align:center;','int'),
			array($ar_idade['PA'],'text-align:center;','int'),
			array(($ar_idade['AT'] + $ar_idade['AS'] + $ar_idade['PA']),'text-align:center;','int'),
			array($ar_idade['PE'],'text-align:center;','int')
		);
		
		$nr_conta_idade++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->id_tabela  = 'tabela_idade';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();
	

?>