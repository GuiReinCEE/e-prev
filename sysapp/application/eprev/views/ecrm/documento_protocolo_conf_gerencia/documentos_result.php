<?php
	$head = array( 
        'Protocolo',
		'Envio',
		'Usuário Envio',
		'Recebimento',
		'Usuário Receb.',
		'Indexação',
		'RE',
		'Participante',
		'Doc',
		'Cód. Doc.',
		'ID',
		'Caminho',
		'Observação',
		'Páginas',
		'Processo',
		'Arquivo',
		'Status',
		'Dt. Conferência',
		'Usuário Conferência',
		'Qtd. Registros.',
		''
    );

    $body = array();

    foreach($collection as $key => $item)
    {
    	$body[] = array(
            $item["nr_protocolo"],
			$item['dt_envio'],
			array($item['ds_usuario_envio'], 'text-align:left;'),
			$item['dt_recebimento'],
			array($item['ds_usuario_recebimento'], 'text-align:left;'),
	    	$item['dt_indexacao'],
	    	$item['nr_re'],
	    	array($item['ds_participante'], 'text-align:left;'),
	    	array($item['ds_documento'], 'text-align:left;'),
	    	$item['cd_tipo_doc'],
	    	$item['nr_id_contrato'],
	    	array($item['ds_caminho'], 'text-align:left;'),
	    	array($item['observacao'], 'text-align:justify;'),
	    	$item['nr_folha'],
	    	array($item['ds_processo'], 'text-align:left;'),
	    	array($item['arquivo_nome'], 'text-align:left;'),
	    	'<span class="'.$item['ds_label_status'].'">'.$item['ds_status'].'</span>',
	    	$item['dt_conferencia'],
	    	$item['ds_usuario_conferencia'],
	    	'<span class="badge badge-info">'.$item['qtd_acomp'].'</span>',
	    	anchor('ecrm/documento_protocolo_conf_gerencia/acompanhamento/'.$item['cd_documento_protocolo_conf_gerencia_item'], '[acompanhamento]')
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    
    echo $grid->render();