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
		'Ajuste',
		'Qtd. Acomp.',
		''
    );

    $body = array();

    foreach($collection as $key => $item)
    {
	    $arquivo = '';
	    if(trim($item['arquivo']) != '')
	    {
	        $arquivo = anchor(base_url() . 'up/protocolo_digitalizacao_'.intval($item['cd_documento_protocolo']).'/' . $item['arquivo'], $item['arquivo_nome'], array('target' => '_blank'));
	    }

	    if(trim($item['fl_status']) == 'P')
	    {
			$dropdown = form_dropdown('cd_documento_conferencia_'.$item['cd_documento_protocolo_conf_gerencia_item'], $drop_status, array($item['fl_status']), 'onchange="valida_status('.$item['cd_documento_protocolo_conf_gerencia_item'].')"');

    		$dropdown .= '<span id="documento_conferencia_'.$item['cd_documento_protocolo_conf_gerencia_item'].'" style="display:none"></span>';
	    }
	    else if(trim($item['fl_status']) == 'C')
    	{
    	    $dropdown = $item['dt_conferencia'];
    	}
	    else
	    {
	    	$dropdown = 'Solicitação de Ajuste';
	    }

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
	    	array($arquivo, 'text-align:left;'),
	    	$dropdown,
	    	array('<span id="ocultar_ajuste_'.$item['cd_documento_protocolo_conf_gerencia_item'].'">'.nl2br($item['ds_ajuste']).'</span>', 'text-align:left;'),
	    	'<span class="badge badge-info">'.$item['qtd_acomp'].'</span>',
	    	anchor('ecrm/documento_protocolo_conf_gerencia/acompanhamento/'.$item['cd_documento_protocolo_conf_gerencia_item'], '[acompanhamento]')
	    );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();