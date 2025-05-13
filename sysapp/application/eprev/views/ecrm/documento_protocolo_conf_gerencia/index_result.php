<?php
    $head = array( 
        'Gerência',
        'Responsável',
        '% de Amostragem',
        ''
    );

    $body = array();

    foreach($collection as $key => $item)
    {
    	$body[] = array(
            $item['cd_gerencia'],
            array($item['ds_usuario_responsavel'], 'text-align:left;'),
            number_format($item['nr_amostragem'], 2, ',', '.').' %',
            anchor('ecrm/documento_protocolo_conf_gerencia/cadastro/'.$item['cd_documento_protocolo_conf_gerencia'], '[editar]')
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();