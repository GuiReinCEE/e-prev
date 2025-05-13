<?php
	$head = array(
		'Ano/Mês',
		'Dt. Solicitação',
		'Dt. Limite',
		'Dt. Fechamento',
		'',
		'Qt. Item', 
		'Qt. Anexados', 
		'Qt. Sem Anexo',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$percentual   = 0;
		$qt_sem_anexo = intval($item['qt_item']) - intval($item['qt_anexo']);

	    if(intval($qt_sem_anexo) > 0)
	    {
	    	if(intval($item['qt_item']) > 0)
		    {
		    	$percentual = (intval($item['qt_anexo']) * 100) / intval($item['qt_item']);
		    }
		    else
		    {
		    	$percentual = 0;
		    }
	    }
	    else
	    {
	    	$percentual = 100;
	    }

		$body[] = array(
			$item['nr_ano'].'/'.$item['nr_mes'],
			$item['dt_inclusao'],
			$item['dt_limite'],
			$item['dt_fechamento'],
			progressbar($percentual),
			'<label class="badge badge-success">'.intval($item['qt_item']).'</span>',
			'<label class="badge badge-info">'.intval($item['qt_anexo']).'</label>',
			'<label class="badge badge-important">'.intval($qt_sem_anexo).'</label>',
			(trim($item['dt_fechamento']) == '' ? '<a href="javascript:void(0);" onclick="ir_anexo('.$item['cd_demonstrativo_resultado'].', '.$item['cd_demonstrativo_resultado_mes'].')">[anexar]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>