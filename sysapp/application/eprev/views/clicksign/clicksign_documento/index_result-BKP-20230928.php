<?php
	#echo "<PRE>"; print_r($collection); exit;

	$head = array(
		'Dt. Inclusão',
		'Dt. Atualização',
		'Área',
		'Monitorado Área',
		'Usuário',
		'Situação',
		'Protocolo',
		'#',
		'Descrição'
		
	);

	$body = array();
	
	foreach ($collection as $key => $item) 
	{
		#$url_acoes = anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/".$item["id_doc"], "[consultar]", array('target' => "_blank"));
		#if (in_array($this->session->userdata('divisao'), array('GTI','GCM')) AND ($item['fl_status'] == 'RUNNING')) 
		#if(in_array($this->session->userdata('divisao'), array('GTI','GCM')))
		#{
		#	$url_acoes = br().anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/signatarioEditar/".md5($this->session->userdata('usuario').date("Ymd"))."/".$item["id_doc"], "[editar]", array('target' => "_blank"));
		#}
		
		$fl_check_recusa = FALSE;
		if ($item['fl_status'] == 'RUNNING')
		{
			$fl_check_recusa = TRUE;
		}
		
		$body[] = array(
			$item['dt_inclusao'],
			$item['dt_alteracao'],
			$item['cd_area'],
			'<span class="'.$item["cor_area_monitorar"].'">'.$item["ds_area_monitorar"].'</span>',
			array( $item['nome'], 'text-align:left;'),
			'<span class="'.$item["cor_status"].'">'.$item["ds_status"].'</span>'.($fl_check_recusa == TRUE ? "<br><span id='obDocRecusado_".$item['id_doc']."'>verificando...</span> <script>getRecusa('".$item['id_doc']."')</script>" : ""),
			$item['id_doc'],
			anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/signatarioEditar/".md5($this->session->userdata('usuario').date("Ymd"))."/".$item["id_doc"], (($item['fl_status'] == 'RUNNING') ? "[editar]" : "[consultar]"), array('target' => "_blank")),
			array(utf8_decode($item['documento']), 'text-align:left;'),
			
		);
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>