<?php
	set_title('Cenário legal');
	$this->load->view('header');
?>
<script>
	function lista()
    {
        location.href = "<?= site_url('gestao/controle_cenario/index') ?>";
    }

	function sem_data()
    {
        location.href = "<?= site_url('gestao/controle_cenario/sem_data') ?>";
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
	$abas[] = array('aba_sem_data', 'Sem Data Legal', FALSE, 'sem_data();');
	$abas[] = array('aba_atrasada', 'Atrasada', TRUE, 'location.reload();');

	$head = array(
		'Cenário',
		'Gerência',
		'Dt. Cadastro',
		'Dt. Limite'
	);

	$body = array();

	foreach ($collection as $item)
	{
		$body[] = array(
			array($item['titulo'], 'text-align:left'),
			$item['area'],
			$item['dt_inclusao'],
			$item['dt_limite']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
	echo aba_start($abas);
		echo form_list_command_bar(array());
	    echo $grid->render();
	    echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>