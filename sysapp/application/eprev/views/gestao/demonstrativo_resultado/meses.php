<?php
	set_title('Demonstrativo de Resultados');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/demonstrativo_resultado/index') ?>";
	}

	function ir_estrutura()
	{
		location.href = "<?= site_url('gestao/demonstrativo_resultado/estrutura/'.$demonstrativo['cd_demonstrativo_resultado']) ?>";
	}

	function liberar_mes(cd_mes, ano)
	{
		var confirmacao = 'Deseja liberar o mês?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/demonstrativo_resultado/liberar_mes/'.$demonstrativo['cd_demonstrativo_resultado']) ?>/"+cd_mes+"/"+ano;
		}
	}

	function pdf(cd_mes)
    {
        window.open("<?= site_url('gestao/demonstrativo_resultado/abrir_pdf') ?>/"+cd_mes);
    }

	function fechar_resultado_mes(cd_demonstrativo_resultado_mes)
	{
		var confirmacao = 'Deseja fechar o mês?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/demonstrativo_resultado/fechar_resultado_mes/'.$demonstrativo['cd_demonstrativo_resultado']) ?>/"+cd_demonstrativo_resultado_mes;
		}
	}

	function reabrir_mes(cd_demonstrativo_resultado_mes)
	{
		var confirmacao = 'Deseja reabrir o mês?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('gestao/demonstrativo_resultado/reabrir_mes/'.$demonstrativo['cd_demonstrativo_resultado']) ?>/"+cd_demonstrativo_resultado_mes;
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
	$abas[] = array('aba_meses', 'Meses', TRUE, 'location.reload();');

	$head = array( 
		'Mês',
		'',
		'Qt. Item', 
		'Qt. Anexados', 
		'Qt Sem Anexo', 
		'Status',
		'Dt. Liberação',
		'Dt. Fechamento',
		''
	);

	$body = array();

	foreach($collection as $key => $item)
	{
		$status    = '';
		$link_acao = '';
		$link_mes  = $item['ds_mes'];

		if(trim($item['dt_fechamento']) != '')
	    {
	    	$status = '<label class="label label-info">Fechado</label>';	    	
	    }
	    else if(trim($item['dt_inclusao']) != '')
	    { 
	        $status = '<label class="label label-success">Liberado</label>';   
	    }

	    if(trim($item['dt_inclusao']) == '')
	    {
	    	$link_acao = '<a href="javascript:void(0);" onclick="liberar_mes('.trim($key).' ,'.$demonstrativo['nr_ano'].' )">[liberar]</a>';
	    }
	    else
	    {
	    	$link_mes = anchor('gestao/demonstrativo_resultado/estrutura_mes/'.$demonstrativo['cd_demonstrativo_resultado'].'/'.trim($item['cd_demonstrativo_resultado_mes']), $item['ds_mes']);
	    }

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

	    	if(trim($item['dt_inclusao']) != '')  
			{
	    		if(trim($item['dt_fechamento']) == '')
		    	{
		    		$link_acao = '<a href="javascript:void(0);" onclick="fechar_resultado_mes('.intval($item['cd_demonstrativo_resultado_mes']).' )">[fechar mês]</a>';
		    	}
		    	else
		    	{
		    		$link_acao = anchor(base_url().'up/demonstrativo_resultado/'.$item['arquivo'], '[PDF]', array('target' => '_blank')).
		    		' <a href="javascript:void(0);" onclick="reabrir_mes('.intval($item['cd_demonstrativo_resultado_mes']).' )">[reabrir mês]</a>';;
		    	}
		    }
	    }

	    $body[] = array(
			array($link_mes, 'text-align:left;'),
			(trim($item['dt_inclusao']) != '' ? progressbar($percentual) : ''),
			(trim($item['dt_inclusao']) != '' ? '<label class="badge badge-success">'.intval($item['qt_item']).'</span>' : ''),
			(trim($item['dt_inclusao']) != '' ? '<label class="badge badge-info">'.intval($item['qt_anexo']).'</label>' : ''),
			(trim($item['dt_inclusao']) != '' ? '<label class="badge badge-important">'.intval($qt_sem_anexo).'</label>' : ''),
			$status,
			$item['dt_inclusao'],
			$item['dt_fechamento'],	
			$link_acao
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Demonstrativo');
			echo form_default_hidden('cd_demonstrativo_resultado', '', $demonstrativo['cd_demonstrativo_resultado']);
			echo form_default_row('nr_ano', 'Ano:', '<span class="label label-inverse">'.$demonstrativo['nr_ano'].'</span>');	
		echo form_end_box('default_box');
		echo $grid->render();
		echo br();
	echo aba_end();

	$this->load->view('footer_interna');
?>