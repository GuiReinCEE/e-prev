<?php

    $this->load->helper('grid');

    $head_registro = array(
        'Edição',
        'Registro',
        'Datas',
        'Controle'
    );

    $head_atividade = array(
        'Gerência',
        'Responsável',
        'Dt Implementação',
        'Dt Prevista',
        'Pertinência',
        'Atividade'
    );

    $body_registro = array();

    $grid = new grid();

    $grid_atividade = new grid();

    $grid_atividade->view_count = false;
    $grid_atividade->view_data = false;
 
    foreach($registro as $item)
    {
        $grid_atividade->id_tabela = md5(uniqid(''));

        $body_atividade = array();
        
        foreach($item['atividade'] as $item2)
        {
            $data_impl = $item2['dt_implementacao_norma_legal'];

            if(trim($item2['fl_fora_prazo']) == 'S')
            {
    			$data_impl = '<span class="label label-important" style="white-space: nowrap;">Fora do prazo</span>';
            }
            
            $body_atividade[] = array(
                $item2['area'],
                array($item2['nome'], 'text-align:left;'),
                $data_impl,
                $item2['dt_prevista_implementacao_norma_legal'],
                array('<span class="label '.$item2["cor_status"].'">'.wordwrap($item2['pertinencia'], 50, "<BR>", false).'</span>', 'text-align:left;'),
                anchor(site_url('atividade/atividade_solicitacao/index/'.$item2['area'].'/'.$item2['numero']), $item2['numero'])
            );
        }
    
        $grid_atividade->head = $head_atividade;
        $grid_atividade->body = $body_atividade;
        
        $data = '';

        if(trim($item['dt_cancelamento']) == '')
        {
            if(trim($item['dt_leg']) != '')
            {
                $data .= '<span class="label label-inverse" style="white-space: nowrap;">Legal: '.$item['dt_leg'].'</span>'.br(2);
            }

            if(trim($item['dt_prev']) != '')
            {
                $data .= '<span class="label label-success" style="white-space: nowrap;">Prevista: '.$item['dt_prev'].'</span>'.br(2);
            }

            if(trim($item['dt_impl']) != '')
            {
                $data .= '<span class="label label-info" style="white-space: nowrap;">Implementada: '.$item['dt_impl'].'</span>'.br(2);
            }
        }
        else
        {
            $data = '<span class="label label-important" style="white-space: nowrap;">Cancelada: '.$item['dt_cancelamento'].'</span>'.br(2);
        }

    	$body_registro[] = array(
            $item['cd_edicao'],
            array(anchor(site_url('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$item['cd_edicao'].'/'.$item['cd_cenario']), $item['cd_cenario'] .'-'. $item['titulo']), 'text-align:left'),
            array($data, 'text-align: left;'),
            (count($item['atividade']) > 0 ? $grid_atividade->render() : '')
        );
    }

    $grid->head = $head_registro;
    $grid->body = $body_registro;
    echo $grid->render();
?>