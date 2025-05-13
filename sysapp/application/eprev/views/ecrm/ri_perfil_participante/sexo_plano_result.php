<?php
#echo "<PRE>".print_r($ar_plano,true)."</PRE>";exit;
$nr_conta = 0;
foreach($ar_plano as $ar_item)
{
	$body=array();
	$head = array( 
		'Categoria',  
		'Ativos',
		'Assistidos',
		'Pensão',
		'Total',
		'Pensionistas'
	);

	echo "<h1>".$ar_plano[$nr_conta]['ds_plano']."</h1>";
	
	$nr_conta_idade = 0;
	foreach($ar_categoria as $ar_sexo)
	{
		$body[] = array(
			array($ar_sexo['desc'],'text-align:left;'),
			array($ar_plano[$nr_conta]['AT'][$ar_sexo['codigo']],'text-align:center;','int'),
			array($ar_plano[$nr_conta]['AS'][$ar_sexo['codigo']],'text-align:center;','int'),
			array($ar_plano[$nr_conta]['PA'][$ar_sexo['codigo']],'text-align:center;','int'),
			array(($ar_plano[$nr_conta]['AT'][$ar_sexo['codigo']] + $ar_plano[$nr_conta]['AS'][$ar_sexo['codigo']] + $ar_plano[$nr_conta]['PA'][$ar_sexo['codigo']]),'text-align:center;','int'),
			array($ar_plano[$nr_conta]['PE'][$ar_sexo['codigo']],'text-align:center;','int')
		);
		
		$nr_conta_idade++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->id_tabela  = 'tabela_sexo_plano';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();
	
	$nr_conta++;
}
	

?>