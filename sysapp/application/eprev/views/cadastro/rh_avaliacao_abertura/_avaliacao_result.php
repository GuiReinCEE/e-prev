<?php

	$head = array( 
        'Gerência',
        '',
        'Nome',
        'Dt. Admissão',
        'Cargo / Área de Atuação',
        'Classe / Padrão',
        'Avaliador',
        'Status Avaliado',
        'Status Avaliador',
        'Resultado',
        'Progressão',
        'Promoção',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
    	$avatar_arquivo = trim($item['avatar']);
    	$cd_matriz      = '';
    	$link           = '';
    	$editar         = '';
		
		if(trim($avatar_arquivo) == '')
		{
			$avatar_arquivo = $item['usuario'].'.png';
		}
		
		if(!file_exists('./up/avatar/'.$avatar_arquivo))
		{
			$avatar_arquivo = 'user.png';
		}	

		if(trim($item['ds_status_avaliador']) == 'Finalizado')
		{
			$cd_matriz = $item['cd_matriz'];
			$link      = '<br><a href="javascript:void(0);" onclick="gerar_pdf('.$item['cd_avaliacao_usuario'].')">[termo]</a>';
		}

		if(trim($item['ds_status_avaliador']) == 'Avaliação do Gestor')
		{
			$editar = br().anchor('cadastro/rh_avaliacao_abertura/avaliado/'.$item['cd_avaliacao_usuario'], '[editar]');
		}

        $body[] = array(
			$item['ds_gerencia'],
			'<a href="'.site_url('cadastro/avatar/index/'.intval($item['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img width="50" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>',
			array($item['ds_nome'].br(2).$item['ds_usuario'], 'text-align:left;'),
			$item['dt_admissao'],
			array($item['ds_cargo_area_atuacao'], 'text-align:left'),
			$item['ds_classe'].(trim($item['ds_padrao']) != '' ? ' - '.$item['ds_padrao']: ''),
			array($item['ds_avaliador'], 'text-align:left;'),
			'<label class="'.$item['ds_class_status_avaliado'].'">'.$item['ds_status_avaliado'].'</label>',
			'<label class="'.$item['ds_class_status_avaliador'].'">'.$item['ds_status_avaliador'].'</label>',
			$cd_matriz,
			$item['ds_progressao'],
			$item['ds_promocao'],
			anchor('cadastro/rh_avaliacao/formulario/'.$item['cd_avaliacao_usuario'], '[avaliação]', 'target="_blank"').$link.br().
			anchor('cadastro/rh_avaliacao_abertura/treinamentos/'.$item['cd_avaliacao_usuario'], '[treinamentos]').$editar
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    echo $grid->render();