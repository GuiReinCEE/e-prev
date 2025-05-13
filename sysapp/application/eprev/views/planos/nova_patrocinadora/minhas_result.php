<?php
    $head = array( 
        'Patrocinadora',               
        'Plano',
        'N° Atividade',        
        'Atividade',
        'Descrição',
        'Responsável',
        'Substituto',
        'Dt. Inicio da Atividade',
        'Dt. Prazo',
        'Dt. Encerramento',
        'Usuário'     
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array($item['ds_nome_patrocinadora'], 'text-align:left'), 
            array($item['descricao'], 'text-align:left'),  
            $item['nr_nova_patrocinadora_atividade'],                      
            array(anchor('planos/nova_patrocinadora/minha_atividade/'.$item['cd_nova_patrocinadora'].'/'.$item['cd_nova_patrocinadora_atividade'],$item['ds_nova_patrocinadora_atividade']), 'text-align:left'),
            array(nl2br($item['ds_atividade']), 'text-align:justify'),
            array($item['ds_usuario_responsavel'], 'text-align:left'),
            array($item['ds_usuario_substituto'], 'text-align:left'),
            $item['dt_inicio'],
            '<label class="label label-'.trim($item['ds_class_prazo']).'">'.$item['dt_prazo'].'</label>',
            $item['dt_encerramento'],
            array($item['ds_usuario_encerramento'], 'text-align:left')  
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    echo $grid->render();
?>