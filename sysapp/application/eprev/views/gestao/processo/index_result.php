<?php
	$head = array( 
		'#',
		'Processo',
		'Qt Indicador',
		'Vigente',
		'Dt Ini. Vigência',
		'Dt Fim Vigência',
		'Responsável'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
		   '<span class="'.$item['ds_class_versao'].'">'.$item['cd_processo'].'</span>',
			array(anchor(site_url('gestao/processo/cadastro/'.$item['cd_processo']), $item['procedimento'], 'style="color:'.$item['ds_color_versao'].'"'), 'text-align:left;'),
			anchor(site_url('gestao/processo/indicador/'.$item['cd_processo']), $item['qt_indicador'], 'style="color:'.$item['ds_color_versao'].'"'),
		    '<span class="'.$item['ds_class_vigente'].'">'.$item['ds_vigente'].'</span>',
		    '<span class="'.$item['ds_class_versao'].'">'.$item['dt_ini_vigencia'].'</span>',
		    '<span class="'.$item['ds_class_versao'].'">'.$item['dt_fim_vigencia'].'</span>',
		    array('<span class="'.$item['ds_class_versao'].'">'.$item['ds_responsavel'].'</span>', 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>