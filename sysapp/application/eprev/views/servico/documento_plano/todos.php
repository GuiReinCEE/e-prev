<?php
	set_title('Documentos Site');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('servico/documento_plano') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('servico/documento_plano/cadastro/'.$row['cd_documento_plano']) ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
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
		ob_resul.sort(1, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_anteriores', 'Todos Documentos', TRUE, 'location.reload();');

	$head = array( 
		'Arquivo',
		'Dt Inclusão',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{	
	    $body[] = array(
			array(anchor(base_url().'up/documento_plano/'.$item['arquivo_nome'], $item['ds_documento_plano_tipo'], array('target' => '_blank')), 'text-align:left;'),
			$item['dt_inclusao'],
			array($item['ds_usuario_inclusao'], "text-align:left;")
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open();

			echo form_start_box('default_box', 'Documento Site');
				echo form_default_hidden('cd_documento_plano', '', $row['cd_documento_plano']);
				echo form_default_row('ds_documento_plano', 'Documento Site:', $row['ds_documento_plano']);
				echo form_default_row('ds_documento_plano_tipo', 'Tipo Documento:', $ds_documento);
			echo form_end_box('default_box');

		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();

$this->load->view('footer');
?>