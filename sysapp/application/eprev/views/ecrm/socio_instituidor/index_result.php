<?php
    $head = array( 
    	'Cód.',  
    	'Nome', 
    	'CPF',
        'CPF Participante',
        'Instituidor',
        'Dt Cadastro',
    	'Dt Envio',
    	'Dt Validação',
    	'Situação',
        'Categoria',
        'Usuário Validação',
    	'Usuário Cadastro',
        'Indicação Interna'
    );

    $body = array();



    foreach($collection as $item)
    {
        if($item['ds_usuario_indicacao'] != '')
        {
            $gerencia = $item['ds_gerencia_indicacao'] . ' - ' .  $item['ds_usuario_indicacao'];
        }
        else
        {
            $gerencia = $item['ds_gerencia_indicacao'];
        }

        $body[] = array(
            anchor('ecrm/socio_instituidor/cadastro/'.$item['cd_socio_instituidor_pacote'], $item['cd_socio_instituidor_pacote']),
            array(anchor('ecrm/socio_instituidor/cadastro/'.$item['cd_socio_instituidor_pacote'], $item['nome']), 'text-align:left;'),
            $item['cpf'],
            $item['cpf_participante'],
            array($item['ds_empresa'], 'text-align:left;'), 
            $item['dt_inclusao'],
    		$item['dt_envio'],
            '<span class="label label-info">'.trim($item['dt_validacao']).'</span>',
            '<span class="label '.trim($item['class_socio']).'">'.trim($item['ds_socio']).'</span>',
            $item['ds_socio_instituidor_categoria'],
            array($item['ds_nome_validacao'], 'text-align:left;'),
    		array($item['ds_nome_inclusao'], 'text-align:left;'),
            $gerencia
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>