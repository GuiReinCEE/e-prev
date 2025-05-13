<?php
set_title('Aviso - Histórico');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?= (trim($fl_diretoria) == 'S' ? site_url("gestao/gestao_aviso/aviso_diretoria")  : site_url("gestao/gestao_aviso")) ?>';
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

	$body = array();
	$head = array('Dt Referência','Dt Verificação','Usuário');

	foreach( $collection as $item )
	{
		$body[] = array(
			$item["dt_referencia"],
			$item["dt_verificacao"],
			array($item["usuario"], "text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	$head = array(
		'Acompanhamento',
	   	'Dt Inclusão',
	   	'Usuário'
	);

	$body = array();

	foreach($acompanhamento as $item)
	{
		$body[] = array(
			array(nl2br($item["ds_gestao_aviso_verificacao_acompanhamento"]), "text-align:justify;"),
			$item["dt_inclusao"],
			array($item["ds_usuario_inclusao"], "text-align:left;")
		);
	}

	$grid2 = new grid();
	$grid2->head = $head;
	$grid2->body = $body;


	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_verificar', 'Histórico', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_start_box("default_box", "Histórico");
			echo form_default_row('cd_gestao_aviso', "Código:", '<span class="label label-inverse">'.intval($row['cd_gestao_aviso']).'</span>');
			echo form_default_row('', "Descrição:", '<span class="label label-inverse">'.$row['ds_descricao'].'</span>');
		echo form_end_box("default_box");
		echo $grid->render();
		echo br(2);
		echo form_start_box("default_acompanhamento_box", "Acompanhamento");
		echo $grid2->render();
		echo form_end_box("default_acompanhamento_box");
		echo br(5);
	echo aba_end();
$this->load->view('footer_interna');
?>