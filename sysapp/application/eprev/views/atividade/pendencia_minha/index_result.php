<?php
    $head = array( 
        'Cód',
        'Descrição',
        'Dt. Limite',
        'Atrasada',
        'Responsável 1',
        'Responsável 2'
    );

    $body = array();

    foreach($collection as $item)
    {
        $descricao = $item['ds_descricao'];

        if(trim($item['cd_pendencia']) == 'RT')
        {
            $descricao = utf8_decode($descricao);
        }
			
		if((trim($item['link']) == '') OR (trim($item['link']) == '#'))
		{
			$link = (
				((intval($item['cd_responsavel']) == $this->session->userdata('codigo')) AND (($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('indic_01') == 'S')))
					?
					array('<span style="color:blue; font-size: 120%; font-weight: bold;">'.nl2br($descricao).'</span>', 'text-align:left;')
					:
					array(nl2br($descricao), 'text-align:left;')
				);
		}
		else
		{
			$link = (
				((intval($item['cd_responsavel']) == $this->session->userdata('codigo')) AND (($this->session->userdata('tipo') == 'D') OR ($this->session->userdata('tipo') == 'G') OR ($this->session->userdata('indic_01') == 'S')))
					?
					array(anchor($item['link'], '<span style="color:blue; font-size: 120%;">'.nl2br($descricao).'</span>'), 'text-align:left;')
					:
					array(anchor($item['link'], nl2br($descricao)), 'text-align:left;')
				);			
		}

        $body[] = array(
            $item['cd_pendencia'],
            $link,
    		'<span class="label '.trim($item['cor_limite']).'">'.trim($item['dt_limite']).'</span>',
            '<span class="label '.(trim($item['fl_atrasada']) == "S" ? 'label-important' : '').'">'.trim($item['ds_atrasada']).'</span>',
            $item['ds_responsavel'],
            $item['ds_substituto']
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo $grid->render();
?>