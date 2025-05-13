<?php
    $head = array( 
        'Empresa',
        'Plano',
        'Arquivo',
        'Documento',
		'LGPD',
        'Doc. Aprovação',
        'Termo Aditivo',
        'Portaria de Aprovação Termo Aditivo',
        'Termo de Adesão',
        'Portaria de Aprovação Termo Adesão',
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            array(anchor('gestao/convenio_adesao/cadastro/'.$item['cd_convenio_adesao'], $item['empresa']), 'text-align:left;'),
            array(anchor('gestao/convenio_adesao/cadastro/'.$item['cd_convenio_adesao'], $item['plano']), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_convenio_adesao'], 'text-align:left;'),
			$item['ds_lgpd'],
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_aprovacao'], $item['arquivo_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_termo_aditivo'], $item['arquivo_termo_aditivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_portaria_aprovacao'], $item['arquivo_portaria_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_termo_adesao'], $item['arquivo_termo_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
            array(anchor(base_url().'up/convenio_adesao/'.$item['arquivo_portaria_aprovacao_adesao'], $item['arquivo_portaria_aprovacao_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
			
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();