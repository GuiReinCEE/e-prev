<?php
    $head = array( 
        'Competência/Fator de Desempenho',
        'Plano para Melhoria do Desempenho',
        'Resultado Esperado',
        'Responsável (Quem)',
        'Quando (Prazo)',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            array(nl2br($item['ds_avaliacao_usuario_plando_desenvolvimento']), 'text-align:justify'),
            array(nl2br($item['ds_plano_melhoria']), 'text-align:justify'),
            array(nl2br($item['ds_resultado']), 'text-align:justify'),
            array($item['ds_responsavel'], 'text-align:left'),
            array($item['ds_quando'], 'text-align:left'),
            '<a href="javascript:void(0);" onclick="adicionar('.$item['cd_avaliacao_usuario_plando_desenvolvimento'].')">[editar]</a>'.
            (trim($item['fl_formulario']) != 'S' ? br().'<a href="javascript:void(0);" style="color:red;" onclick="excluir('.$item['cd_avaliacao_usuario_plando_desenvolvimento'].')">[excluir]</a>' : '')
            
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
    $grid->view_count = FALSE;

    if(trim($row['dt_encerramento']) != '')
    {
    	$grid->col_oculta = array(5);
    }

	echo br();
    echo $grid->render();
    
    if($fl_adicionar)
    {
        echo button_save('Adicionar', 'adicionar()');
        echo button_save('Mostrar Treinamentos', 'mostrar()', 'botao_disabled', 'id="btn_mostrar"');
        echo button_save('Ocultar Treinamentos', 'ocultar()', 'botao_amarelo', 'id="btn_ocultar" style="display:none;"');
    }
    