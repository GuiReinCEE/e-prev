<?php
    $head = array( 
        'Ano',
        'Dt. Íncio',
        'Dt. Encerramento',
        'Dt. Inclusão',
        'Usuário',
        'Dt. Envio', 
        'Usuário Envio',
        'Qt. Avaliações',
        'Qt. Avaliações Encerradas',
        '%',
        'Média dos Resultados',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['nr_ano_avaliacao'],
            $item['dt_inicio'],
            $item['dt_encerramento'],
            $item['dt_inclusao'],
            array($item['ds_usuario'], 'text-align:left;'),
            $item['dt_envio_email'],
            $item['ds_usuario_envio_email'],
            '<span class="badge badge-warning">'.$item['qt_avaliacao'].'</span>',
            '<span class="badge badge-success">'.$item['qt_avaliacao_encerrada'].'</span>',
            progressbar(((intval($item['qt_avaliacao_encerrada']) * 100) / intval($item['qt_avaliacao']))),
            '<span class="label label-success">'.number_format($item['nr_media_resultado'], 2, ',', '.').'</span>',
            anchor('cadastro/rh_avaliacao_abertura/cadastro/'.$item['cd_avaliacao'], '[editar]')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();