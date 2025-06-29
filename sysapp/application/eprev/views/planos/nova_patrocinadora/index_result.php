<?php
    $head = array( 
        'N� Atividade',
        '',
        'Atividade',
        'Descri��o',
        'Ger�ncia Resp.',
        'Respons�vel',
        'Substituto',
        'Prazo (dias)',
        'Atividades Dependentes',
        'Observa��es',
        'Dt. Desativado'
    );

    $body = array();

    foreach($collection as $item)
    {
        if($item['cd_usuario_responsavel'] == $cd_usuario OR $item['cd_usuario_substituto'] == $cd_usuario OR $cd_divisao == 'GP' OR $cd_usuario = 251)
        {
            $link = array(anchor('planos/nova_patrocinadora/cadastro/'.$item['cd_nova_patrocinadora_estrutura'], $item['ds_nova_patrocinadora_estrutura']), 'text-align:left');
        }
        else
        {
            $link = array($item['ds_nova_patrocinadora_estrutura'], 'text-align:left');
        }
        
        $config = array(
            'name'   => 'nr_nova_patrocinadora_estrutura_'.$item['cd_nova_patrocinadora_estrutura'], 
            'id'     => 'nr_nova_patrocinadora_estrutura_'.$item['cd_nova_patrocinadora_estrutura'],
            'onblur' => "set_ordem(".$item['cd_nova_patrocinadora_estrutura'].");",
            'style'  => "display:none; width:50px;"
        );
        
        $body[] = array(
            '<span id="ajax_ordem_valor_'.$item['cd_nova_patrocinadora_estrutura'].'"></span> '.'<span id="ordem_valor_'.$item['cd_nova_patrocinadora_estrutura'].'">'.$item['nr_nova_patrocinadora_estrutura'].'</span>'.
            form_input($config, $item['nr_nova_patrocinadora_estrutura'])."
            <script> 
                jQuery(function($){ 
                    $('#cd_nova_patrocinadora_estrutura_".$item['cd_nova_patrocinadora_estrutura']."').numeric(); 
                }); 
            </script>",
            
           '<a id="ordem_editar_'.$item['cd_nova_patrocinadora_estrutura'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_nova_patrocinadora_estrutura'].');" title="Editar a ordem">[editar]</a>
            <a id="ordem_salvar_'.$item['cd_nova_patrocinadora_estrutura'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>',

            $link,
            array(nl2br($item['ds_atividade']), 'text-align:justify'),
            $item['cd_gerencia'],
            array($item['ds_usuario_responsavel'], 'text-align:left'),
            array($item['ds_usuario_substituto'], 'text-align:left'),
            $item['nr_prazo'],
            array(implode(br(),$item['ds_atividade_dependente']), 'text-align:justify'),
            array(nl2br($item['ds_observacao']), 'text-align:justify'),
            $item['dt_desativado']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>