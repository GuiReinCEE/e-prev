<?php
    $head = array(
        'RE',
        'Nome Participante',
        'Origem',
        'Dt. Encaminhamento',
        'Tipo Documento',
        'Documento',
        'Observações'
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
            array($item['nome'], 'text-align:left'),
            $item['ds_origem'],
            $item['dt_encaminhamento'],
            array($item['ds_tipo'], 'text-align:left'),
            anchor(base_url('up/autoatendimento_documento_recebido/'.$item['documento']), $item['documento'], array('target' => "_blank")),
            array(nl2br($item['ds_observacao']), 'text-align:justify'),
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;
    echo $grid->render();
?>