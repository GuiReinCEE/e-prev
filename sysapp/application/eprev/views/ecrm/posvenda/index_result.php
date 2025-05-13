<?php
    $head = array( 
    	'',
    	'RE',
    	'Nome',
    	'Acompanhamento',
    	'Dt Ingresso',
        'Dt Cad. Ingresso',
        'Dt Envio Boas Vindas',
        'Dt Envio Pós-Venda',
    	'Usuário',
    	'Dt. Enc. Pós-Venda',
    	'Usuário',
    	'Protocolo',
        'Vendedor',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $link = '';

        if(intval($item['cd_pos_venda_participante']) == 0)
        {
            $link = anchor('ecrm/posvenda/posvenda_participante/'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'], '[iniciar]');
        }
        else
        {
            if(trim($item['dt_final']) == '')
            {
                $link = anchor('ecrm/posvenda/posvenda_participante/'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'], '[continuar]');
            }   
            else
            {
                $link = anchor('ecrm/posvenda/resposta/'.$item['cd_pos_venda_participante'], '[abrir]');
            }
        }

    	$body[] = array(
            $link,
            $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
            array($item['nome'], 'text-align:left;'),
            $item['dt_acompanhamento'],
            '<span class="label">'.$item['dt_ingresso'].'</span>',
            '<span class="label label-warning">'.$item['dt_digita_ingresso'].'</span>',
            '<span class="label label-info">'.$item['dt_boas_vindas'].'</span>',
            '<span class="label label-success">'.$item['dt_inicio'].'</span>',
            array($item['ds_usuario_inicio'], 'text-align:left;'),
            '<span class="label label-inverse">'.$item['dt_final'].'</span>',
            array($item['ds_usuario_final'], 'text-align:left;'),
            ($item['cd_atendimento'] != '' ?  anchor('ecrm/atendimento_lista/atendimento/'.$item['cd_atendimento'], $item['cd_atendimento']) : ''),
            array($item['ds_usuario_vendedor'], 'text-align:left;'),
            (intval($item['cd_pos_venda_participante']) > 0 ? '<a href="javascript:void(0);" onclick="excluir('.intval($item['cd_pos_venda_participante']).')">[excluir]</a>' : ''),
    	);
    }
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>
