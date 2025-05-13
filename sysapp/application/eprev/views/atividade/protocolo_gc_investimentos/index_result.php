<?php
    $head = array( 
        'Cуd',
        'Documento',
        'Percentual',
        'Retorno - AR',
        'Dt Envio',
        'Dt Recebido',
        'Dt Envio SG',
        'Dt Expediзгo',
    	'Dt Recusado',
        'Dt Encerrado'
    );

    $body = array();

    foreach($collection as $item)
    {
        $retorno = (trim($item['fl_retorno']) == 'S' ? 'Se aplica' : 'Nгo se aplica');

        if(trim($item['fl_retorno']) == 'S' AND trim($item['arquivo']) != '')
        {
            $retorno .= ' - '.anchor(base_url().'up/protocolo_gc_investimentos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank'));
        }

        $total = 0;

        $total += (trim($item['dt_envio_gc']) != '' ? 1 : 0);
        $total += (trim($item['dt_recebido']) != '' ? 1 : 0);
        $total += (trim($item['dt_envio_sg']) != '' ? 1 : 0);
        $total += (trim($item['dt_expedicao']) != '' ? 1 : 0);
        $total += (trim($item['dt_encerrar']) != '' ? 1 : 0);

        if(trim($item['fl_retorno']) == 'S')
        {
            $total += (trim($item['arquivo']) != '' ? 1 : 0);
            
            $percent = (intval($total) * 100) / 6;
        }
        else
        {
            $percent = (intval($total) * 100) / 5;
        }

        $body[] = array(
            anchor('atividade/protocolo_gc_investimentos/cadastro/'.$item['cd_protocolo_gc_investimentos'], $item['cd_protocolo_gc_investimentos']),
            array(anchor('atividade/protocolo_gc_investimentos/cadastro/'.$item['cd_protocolo_gc_investimentos'], $item['documento']),'text-align:left'),
            progressbar(intval($percent)),
            $retorno,
            $item['dt_envio_gc'],
            $item['dt_recebido'],
            $item['dt_envio_sg'],
            $item['dt_expedicao'],
            $item['dt_recusado'],
            $item['dt_encerrar']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>