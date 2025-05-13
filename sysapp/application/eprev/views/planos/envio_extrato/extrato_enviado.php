<?php
	set_title('Extrato Enviado');
	$this->load->view('header');
?>

<script>
	<?= form_default_js_submit(array('dt_envio'), 'enviar_email(form)') ?>

	function ir_enviado()
	{
		location.href = "<?= site_url('planos/envio_extrato/enviado') ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "RE",
		    "CaseInsensitiveString",
		    null
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
		ob_resul.sort(1, false);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_liberados', 'Lista', FALSE, 'ir_enviado();');
	$abas[] = array('aba_enviado', 'Extrato Enviado', TRUE, 'location.reload();');

	$head = array( 
		'RE',
		'Nome',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'], 'text-align:left;'),
			anchor('ecrm/reenvio_email/index/'.$item['cd_email'], '[ver e-mail]', 'target="_blank"')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Extrato');
			echo form_default_row('', 'Plano:', $extrato['ds_plano']);
			echo form_default_row('', 'Empresa:', $extrato['ds_empresa']);
			echo form_default_row('', 'Nr. Extrato:', $extrato['nr_extrato']);
			echo form_default_row('', 'Dt. Base:', '<span class="label">'.$extrato['dt_base'].'</span>');
			echo form_default_row('', 'Dt. Gerado:', '<span class="label">'.$extrato['dt_inclusao'].'</span>');
			echo form_default_row('', 'Usuário:', $extrato['ds_usuario']);
			echo form_default_row('', 'Qt. Extratos Eletro:', '<span class="badge badge-success">'.$extrato['qt_extrato_eletro'].'</span>');
			echo form_default_row('', 'Dt. Envio:', '<span class="label label-inverse">'.$extrato['dt_agendado'].'</span>');
			echo form_default_row('', 'Qt. Enviados:', '<span class="badge badge-info">'.$extrato['qt_enviado'].'</span>');
			echo form_default_row('', 'Qt. Aguardando Envio:', '<span class="badge badge-success">'.$extrato['qt_aguardando'].'</span>');
			echo form_default_row('', 'Qt. Não enviados:', '<span class="badge badge-important">'.$extrato['qt_enviado_nao'].'</span>');
		echo form_end_box('default_box');
		echo br();
		echo $grid->render();
	echo aba_end();

	$this->load->view('footer');
?>