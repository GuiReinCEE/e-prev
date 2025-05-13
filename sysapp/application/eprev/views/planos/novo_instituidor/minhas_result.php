<?php
    $head = array( 
        'Instituidor',               
        'Plano',
        'N� Atividade',        
        'Atividade',
        'Descri��o',
        'Respons�vel',
        'Substituto',
        'Dt. Inicio da Atividade',
        'Dt. Prazo',
        'Dt. Encerramento',
        'Usu�rio'     
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array($item['ds_nome_instituidor'], 'text-align:left'), 
            array($item['descricao'], 'text-align:left'),  
            $item['nr_novo_instituidor_atividade'],                      
            array(anchor('planos/novo_instituidor/minha_atividade/'.$item['cd_novo_instituidor'].'/'.$item['cd_novo_instituidor_atividade'],$item['ds_novo_instituidor_atividade']), 'text-align:left'),
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