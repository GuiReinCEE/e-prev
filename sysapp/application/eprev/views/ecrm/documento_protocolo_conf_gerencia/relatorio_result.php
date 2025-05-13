<?php
    $head = array( 
        'Dt. Referência',
        'Dt. Início',
		'Dt. Limite',
		'Gerência',
        '',
		'Qt. Doc. Indexados',
        'Qt. Doc. p/ Conf.',
		'Qt. Doc. Conferido',
		'Qt. Doc. Pendentes',
        'Qt. Doc. p/ Ajustes',
        'Qt. Doc. Ajustados',
		'Qt. Acompanhamentos',
		''
    );

    $body = array();

    foreach($collection as $key => $item)
    {
    	$qt_conferencia = (intval($item['qt_conferencia']) > 0 ? intval($item['qt_conferencia']) : 1);

    	$body[] = array(
            $item['dt_referencia'],
            $item['dt_inclusao'],
    		$item['dt_limite'],
    		$item['cd_gerencia'],
            progressbar(((intval($item['qt_conferido']) * 100) / $qt_conferencia)),
    		'<span class="badge badge-info">'.$item['qt_indexados'].'</span>',
            '<span class="badge badge-important">'.$item['qt_conferencia'].'</span>',
    		'<span class="badge badge-warning">'.$item['qt_conferido'].'</span>',
    		'<span class="badge">'.$item['qt_conferencia_pendente'].'</span>',
            '<span class="badge badge-inverse">'.$item['qt_ajuste'].'</span>',
            '<span class="badge badge-success">'.$item['qt_ajustado'].'</span>',
    		'<span class="badge badge-info">'.$item['qt_acompanhamento'].'</span>',
    		anchor('ecrm/documento_protocolo_conf_gerencia/documentos/'.$item['cd_documento_protocolo_conf_gerencia_item_mes'], '[documentos]')
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();