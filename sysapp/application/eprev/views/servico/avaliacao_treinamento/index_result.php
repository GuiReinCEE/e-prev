<?php
	$head = array( 
		'Colaborador',
		'Treinamento',
		'Promotor',
		'Dt. In�cio',
		'Dt. Final',
		'Status',
		'Dt. Envio Avalia��o',
		'Dt. Termino da Avalia��o',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
			$item['colaborador'],
			array($item['nome'],'text-align:left'),
	        array($item['promotor'],'text-align:left'),
	        $item['dt_inicio'],
	        $item['dt_final'],
	        '<span class="'.$item['status_label'].'">'.$item['status'].'</span>',
	        $item['dt_inclusao'],
	        $item['dt_finalizado'],
	        (trim($item['fl_finalizado']) != '' ? 
	        	(trim($item['fl_finalizado']) == 'N' ? anchor(site_url('servico/avaliacao_treinamento/cadastro/'.$item['cd_treinamento_colaborador_resposta']), '[Responder Avalia��o]') : anchor(site_url('servico/avaliacao_treinamento/pdf/'.$item['cd_treinamento_colaborador_resposta']), '[PDF]', array('target' => "_blank")))
    		: '')
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>