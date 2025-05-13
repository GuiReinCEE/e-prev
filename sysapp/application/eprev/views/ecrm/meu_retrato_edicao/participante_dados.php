<?php
	set_title('Meu Retrato Edição - Participante Dados');
	$this->load->view('header');
?>
<script>


	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, false);
	}

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/cadastro/'.$row['cd_edicao']) ?>";
    }
	
    function ir_verificar()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/verificar/'.$row['cd_edicao']) ?>";
    }	

    function ir_participante()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante/'.$row['cd_edicao']) ?>";
    }	
				
	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante();');
    $abas[] = array('aba_participante', 'Participante Dados', TRUE, 'location.reload();');
	
	if(gerencia_in(array('GTI')))
	{
		$abas[] = array('aba_verificar', 'Verificar', FALSE, 'ir_verificar();');
	}		

	$head = array(
		'Código',
		'Descrição',
		'Valor'
	);

	$body = array();

	$id = 0;
	
	foreach ($collection as $item)
	{	
		$body[] = array(
			array($item['cd_linha'], 'text-align:left'),
			array($item['ds_linha'], 'text-align:left'),
			array($item['vl_valor'], 'text-align:left')
			
		);

		$id++;
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		

		echo '<div id="result_div">'.$grid->render().'</div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>