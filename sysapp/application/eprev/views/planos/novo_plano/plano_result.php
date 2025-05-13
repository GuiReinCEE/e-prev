<?php
    $head = array( 
        'Nome do Plano',
        'Dt. Limite Aprovação',
        'Dt. Início Atividade',
        'Dt. Encerramento',
        'Qt. Atividades',
        'Qt. Atividades Encerradas',
        'Qt. Atividades Abertas',
        '%'
    );

    $body = array();

    foreach($collection as $item)
    {
        $percentual = 0;

        if($item['qt_atividade'] > 0)
        {
            $percentual = (($item['qt_atividades_encerradas'] / $item['qt_atividade']) * 100);
        }

        $body[] = array(
            array(anchor('planos/novo_plano/plano_cadastro/'.$item['cd_novo_plano'], $item['ds_nome_plano']), 'text-align:left'),
            $item['dt_limite_aprovacao'],
            $item['dt_inicio'],
            $item['dt_encerramento'],
            '<label class="badge badge-success">'.$item['qt_atividade'].'</label>',
            '<label class="badge badge-info">'.$item['qt_atividades_encerradas'].'</label>',
            '<label class="badge badge-important">'.$item['qt_atividades_abertas'].'</label>',
            progressbar($percentual)
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>