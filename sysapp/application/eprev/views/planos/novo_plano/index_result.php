<?php
    $head = array( 
        'Ordem',
        '',
        'Subprocesso',
        'Descrição',
        'Dt. Desativado'
    );

    $body = array();

    foreach($collection as $item)
    {        
        $config = array(
            'name'   => 'nr_ordem'.$item['cd_novo_plano_estrutura'], 
            'id'     => 'nr_ordem'.$item['cd_novo_plano_estrutura'],
            'onblur' => "set_ordem(".$item['cd_novo_plano_subprocesso'].", ".$item['cd_novo_plano_estrutura'].");",
            'style'  => "display:none; width:50px;"
        );

        $body[] = array(            
            '<span id="ajax_ordem_valor_'.$item['cd_novo_plano_estrutura'].'"></span> '.'<span id="ordem_valor_'.$item['cd_novo_plano_estrutura'].'">'.$item['nr_ordem'].'</span>'.
            form_input($config, $item['nr_ordem'])."
            <script> 
                jQuery(function($){ 
                    $('#cd_novo_plano_estrutura".$item['cd_novo_plano_estrutura']."').numeric(); 
                }); 
            </script>",
                        
           '<a id="ordem_editar_'.$item['cd_novo_plano_estrutura'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_novo_plano_estrutura'].');" title="Editar a ordem">[editar]</a>
            <a id="ordem_salvar_'.$item['cd_novo_plano_estrutura'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>',
            
            array(anchor('planos/novo_plano/cadastro/'.$item['cd_novo_plano_estrutura'], nl2br($item['ds_novo_plano_subprocesso'])), 'text-align:left'),   
            array(anchor('planos/novo_plano/cadastro/'.$item['cd_novo_plano_estrutura'], nl2br($item['ds_novo_plano_estrutura'])), 'text-align:justify'),
            $item['dt_encerramento']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>