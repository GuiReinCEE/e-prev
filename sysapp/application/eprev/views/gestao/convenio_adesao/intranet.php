<script>

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$this->load->helper('grid');

    $head = array( 
        'Empresa',
        'Arquivo',
        'Documento',
        'Doc. Aprovação',
        'Termo Aditivo',
        'Portaria de Aprovação Termo Aditivo',
        'Termo de Adesão',
        'Portaria de Aprovação Termo Adesão',
        'Versões Anteriores'
    );

	$head2 = array(
        'Arquivo',
        'Documento',
        'Doc. Aprovação',
        'Termo Aditivo',
        'Portaria de Aprovação Termo Aditivo',
        'Termo de Adesão',
        'Portaria de Aprovação Termo Adesão'
	);

	echo aba_start($abas);
		echo '<h2 style="font-size:120%">Gerência Responsável : GAP</h2>';
		echo '<h2 style="font-size:120%">Publicado no Site :  Não</h2>';
	    $body  = array();

		foreach ($collection as $key => $item) 
		{
			$body  = array();
			$body2 = array();
			
			if(count($item['convenio']) > 0)
			{
				echo '<h2 style="margin: 0px; padding-top: 5px; padding-bottom: 5px; color: #0046AD; font-family: calibri, arial; font-size: 18pt;">'.$item['ds_plano'].'</h2>'.br();

				foreach ($item['convenio'] as $key2 => $item2) 
				{
					foreach ($item2['versoes_anteriores'] as $key3 => $item3) 
					{
						$body2[] = array(
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo'], $item3['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
				            array($item3['ds_convenio_adesao'], 'text-align:left;'),
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo_aprovacao'], $item3['arquivo_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo_termo_aditivo'], $item3['arquivo_termo_aditivo_nome'], array('target' => '_blank')), 'text-align:left;'),
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo_portaria_aprovacao'], $item3['arquivo_portaria_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo_termo_adesao'], $item3['arquivo_termo_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
				            array(anchor(base_url().'up/convenio_adesao/'.$item3['arquivo_portaria_aprovacao_adesao'], $item3['arquivo_portaria_aprovacao_adesao_nome'], array('target' => '_blank')), 'text-align:left;')
						);
					}

					$grid2 = new grid();
					$grid2->id_tabela = 'table-2-'.$key;
					$grid2->view_count = false;
					$grid2->head = $head2;
					$grid2->body = $body2;

					$body[] = array(
			            array($item2['empresa'], 'text-align:left;'),
		            	array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo'], $item2['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			            array($item2['ds_convenio_adesao'], 'text-align:left;'),
			            array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo_aprovacao'], $item2['arquivo_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
			            array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo_termo_aditivo'], $item2['arquivo_termo_aditivo_nome'], array('target' => '_blank')), 'text-align:left;'),
			            array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo_portaria_aprovacao'], $item2['arquivo_portaria_aprovacao_nome'], array('target' => '_blank')), 'text-align:left;'),
			            array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo_termo_adesao'], $item2['arquivo_termo_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
			            array(anchor(base_url().'up/convenio_adesao/'.$item2['arquivo_portaria_aprovacao_adesao'], $item2['arquivo_portaria_aprovacao_adesao_nome'], array('target' => '_blank')), 'text-align:left;'),
			            (count($item2['versoes_anteriores']) > 0 ? $grid2->render() : '')
					);
				}

				$this->load->helper('grid');
				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;

				echo $grid->render();
			}
		}

		echo br(2);
	echo aba_end();
?>