<?php
	$head = array( 
		'Ano/Número', 
		'Nome',
		'Promotor',
		'Cidade',
		'UF', 
		'Dt. Início',
		'Dt. Final',
		'Tipo',
		'Qt. Documentos'
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	        $item['numero'],
			array(anchor('servico/treinamentos_documento/documento/'.$item['cd_treinamento_colaborador'],$item['nome']), 'text-align:left'),
	        array($item['promotor'], 'text-align:left'),
	        array($item['cidade'], 'text-align:left'),
	        $item['uf'],
	        $item['dt_inicio'],
	        $item['dt_final'],
	        array($item['ds_treinamento_colaborador_tipo'], 'text-align:left'),
	        '<label class="badge badge-success">'.$item['qt_arquivo'].'</label>'
		);  
	}       	
                
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
           
	echo $grid->render();
?>         