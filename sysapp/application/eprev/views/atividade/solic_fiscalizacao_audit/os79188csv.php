<?php
	set_title('Registro de Solicitações, Fiscalizações e Auditorias - OS:79188');
	$this->load->view('header');
?>
<script>

	function ir_lista()
    {
        location.href = "<?= site_url('atividade/solic_fiscalizacao_audit') ?>";
    }


</script>
<style>
    #artigo_item {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_documentacao', 'OS:79188', TRUE, 'location.reload();');

	$head = array(
		'Ano/Nº',
		'Dt. Recebimento',
		'Documento',
		'Item',
		'Descrição Resumida',
		'Gerência',
        'Responsável',
		'Dt. Atendimento Resp'
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
	  		$item['ds_ano_numero'],
	  		$item['dt_recebimento'],
	  		$item['ds_documento'],
		    $item['nr_item'],
		    array(nl2br($item['ds_solic_fiscalizacao_audit_documentacao']), 'text-align:justify'),
		    $item['cd_gerencia'],
		    implode(br().' - ', $item['responsavel']),
		    $item['dt_atendimento_responsavel']

		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		
		echo br();
		echo $grid->render();

		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>