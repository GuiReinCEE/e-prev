<?php
    $head = array( 
        'Colegiado',
        'Presidente',
        'Secretária',
        'Indicado/Eleito',
        'Nome',
        'Tipo',
		'Cargo',
		'E-mail',
		'Celular',
        'Suplente do Titular',
        'Removido',
        'Dt. Removido',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        if(trim($item['dt_removido']) == '')
        {
            $link = '<a href="javascript:void(0);" onclick="remover('.$item['cd_pauta_sg_integrante'].')">[remover]</a>';
        }
        else
        {
            $link = '<a href="javascript:void(0);" onclick="ativar('.$item['cd_pauta_sg_integrante'].')">[ativar]</a>';
        }

        $body[] = array(
            array(anchor('gestao/pauta_sg_integrante/cadastro/'.$item['cd_pauta_sg_integrante'], $item['ds_colegiado']), 'text-align:left'),
            '<span class="'.$item['ds_label_presidente'].'">'.$item['ds_presidente'].'</span>',
            '<span class="'.$item['ds_label_secretaria'].'">'.$item['ds_secretaria'].'</span>',
            $item['ds_indicado_eleito'],
            array(anchor('gestao/pauta_sg_integrante/cadastro/'.$item['cd_pauta_sg_integrante'], $item['ds_pauta_sg_integrante']), 'text-align:left'),
            (trim($item['fl_secretaria']) == 'N' ? array($item['ds_tipo'], 'text-align:left') : ""),
			array($item['cargo'], 'text-align:left'),
			$item['email'],
			$item['celular'],
            array($item['ds_pauta_sg_integrante_titular'], 'text-align:left'),
            '<span class="'.$item['ds_label'].'">'.$item['ds_removido'].'</span>',
            $item['dt_removido'],
            $link
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();