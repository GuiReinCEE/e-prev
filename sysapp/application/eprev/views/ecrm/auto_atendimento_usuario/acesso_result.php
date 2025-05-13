<?php

	$body=array();
	$head = array( 
		'Cód. Acesso',  
		'RE',
		'Dt Acesso',
		'Local'
	);
	
	$sid_anterior = 0;
	foreach($ar_lista as $ar_item)
	{
		if($ar_item['sid'] != $sid_anterior)
		{
			$body[] = array(
					$ar_item['sid'],
					$ar_item['cd_empresa']."/".$ar_item['cd_registro_empregado']."/".$ar_item['seq_dependencia'],
					$ar_item['dt_acesso'],
					array($ar_item['pagina'],'text-align:left;')
				);			
			$sid_anterior = $ar_item['sid'];
		}
		else
		{
			$body[] = array(
					"",
					$ar_item['cd_empresa']."/".$ar_item['cd_registro_empregado']."/".$ar_item['seq_dependencia'],
					$ar_item['dt_acesso'],
					array($ar_item['pagina'],'text-align:left;')
				);
		}
		 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_auto_atendimento_usuario';
	$grid->head       = $head;
	$grid->body       = $body;
	echo $grid->render();

?>