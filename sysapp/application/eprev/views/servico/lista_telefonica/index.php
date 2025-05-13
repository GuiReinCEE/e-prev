<?php
	set_title('Lista Telefônica');
	$this->load->view('header');
?>
<script>	
    function novo()
    {
        location.href = '<?= site_url('servico/lista_telefonica/csv') ?>';
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$config['button'][] = array('Gerar CSV', 'novo();');
	
	$head = array(
		'Nome',
		'Ramal',
		'Grupo',
		'E-mail'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array($item['nome'], 'text-align:left'),
			array($item['nr_ramal'],'text-align:left'),
			array($item['grupo'],'text-align:left'),
			$item['email']
		);
	}	

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_list_command_bar($config);
		echo $grid->render();
	echo aba_end();

	$this->load->view('footer');
?>