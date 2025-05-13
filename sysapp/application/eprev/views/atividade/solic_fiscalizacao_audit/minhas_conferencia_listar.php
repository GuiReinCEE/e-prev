<?php
	$head = array(
        'Ano/Nº',
        'Nº Item',
        'Descrição Resumida',
		'Origem',
		'Tipo',
        'Documento',
        'Dt. Retorno Solicitante',
        'Dt. Encaminhamento',
        'Usuário',
        'Dt. Atendimento',
        'Status'
    );

    $body = array();

	foreach ($collection as $item)
	{      
	  	$body[] = array(
            anchor('atividade/solic_fiscalizacao_audit/conferencia/'.$item['cd_solic_fiscalizacao_audit_documentacao'], $item['ds_ano_numero']),
            $item['nr_item'],
            array(nl2br($item['ds_solic_fiscalizacao_audit_documentacao']), 'text-align="justify"'),
            array($item['ds_solic_fiscalizacao_audit_origem'], 'text-align="justify"'),
            array($item['ds_solic_fiscalizacao_audit_tipo'], 'text-align="justify"'),
            array($item['ds_documento'], 'text-align="justify"'),
            $item['dt_prazo_retorno'],
            $item['dt_envio_conferencia'],
            array($item['ds_usuario_envio_conferencia'], 'text-align:left'),
            $item['dt_atendimento'],
            '<span class="'.$item['ds_class_label'].'">'.$item['ds_status'].'</span>'
        );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>	