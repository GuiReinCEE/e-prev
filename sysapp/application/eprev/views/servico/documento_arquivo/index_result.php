<?php
    $head = array( 
        'Descrição',
        'Dt Alteração',
        'Arquivo',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            array(anchor('servico/documento_arquivo/cadastro/'.$item['cd_documento_arquivo'], $item['ds_documento_arquivo']), 'text-align:left'),
            $item['dt_alteracao'],
            array(anchor(base_url().'up/documento_arquivo/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            $item['ds_usuario_alteracao'],
            '<a href="javascript:void(0);" onclick="excluir('.$item['cd_documento_arquivo'].')" style="color:red;">[excluir]</a>'
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();