<?php
	$head = array( 
		'Colaborador',
		'Treinamento',
		'Promotor',
		'Dt. Início',
		'Dt. Final',
		'Status',
		'Dt. Envio Avaliação',
		'Dt. Termino da Avaliação',
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
	        	(trim($item['fl_finalizado']) == 'N' ? anchor(site_url('servico/avaliacao_treinamento/cadastro/'.$item['cd_treinamento_colaborador_resposta']), '[Responder Avaliação]') : anchor(site_url('servico/avaliacao_treinamento/pdf/'.$item['cd_treinamento_colaborador_resposta']), '[PDF]', array('target' => "_blank")))
    		: '')
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>