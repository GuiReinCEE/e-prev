<?php
	set_title('Recursos Humanos - Aviso - Histórico');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_aviso') ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateBR",
		    "DateTimeBR",
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
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table()
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_verificar', 'Histórico', TRUE, 'location.reload();');

	$head = array(
		'Dt Referência',
		'Dt Verificação',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['dt_referencia'],
			$item['dt_verificacao'],
			array($item['ds_usuario'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Histórico');
			echo form_default_row('cd_rh_aviso', 'Código:', '<span class="label label-inverse">'.intval($row['cd_rh_aviso']).'</span>');
			echo form_default_row('', 'Descrição:', '<span class="label label-inverse">'.$row['ds_descricao'].'</span>');
		echo form_end_box('default_box');
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>