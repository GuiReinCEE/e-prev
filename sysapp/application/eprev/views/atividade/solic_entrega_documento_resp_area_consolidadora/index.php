<?php
	set_title('Registro de Solicitações de Fiscalizações e Auditorias');
	$this->load->view('header');
?>
<script>

</script>
<?php
    $abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$head = array(
		'Área',
		'Usuário'
	);

    $body = array();


	foreach ($collection as $key => $item)
	{
	  	$body[] = array(
            anchor('atividade/solic_entrega_documento_resp_area_consolidadora/cadastro/'.$item['cd_gerencia'], $item['cd_gerencia']),
            array(implode("<br>", $item['ds_usuario']), 'text-align:left')
		);
    }

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

    echo aba_start($abas);
        echo br(2);
        echo $grid->render();
	echo aba_end();

	$this->load->view('footer'); 
?>