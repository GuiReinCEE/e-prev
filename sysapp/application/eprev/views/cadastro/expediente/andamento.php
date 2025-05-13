<?php
	set_title('Comitê de Ética - Expediente');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_expediente_status', 'ds_expediente_andamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/expediente') ?>";
	}
	
	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/expediente/cadastro/'.$expediente['cd_expediente']) ?>";
	}	
	
	function ir_anexo()
	{
		location.href = "<?= site_url('cadastro/expediente/anexo/'.$expediente['cd_expediente']) ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBR",
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
		ob_resul.sort(0, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_andamento', 'Andamento', TRUE, 'location.reload();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

	$head = array(
		'Dt Cadastro',
		'Status',
		'Andamento',
		'Usuário'
	);

	$body = array();

	foreach($andamento as $item)
	{
		$body[] = array(
			$item['dt_inclusao'],
			$item['ds_expediente_status'],
			array(nl2br($item['ds_expediente_andamento']), 'text-align:left;'),
			array($item['ds_usuario'], 'text-align:left;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;	

	echo aba_start($abas);
		echo form_open('cadastro/expediente/andamento_salvar');	
			echo form_start_box('default_box', 'Expediente');
				echo form_default_hidden('cd_expediente', '', $expediente['cd_expediente']);
				echo form_default_row('', 'Cód Expediente:', '<span class="label label-inverse">'.$expediente['nr_expediente'].'</span>');
				echo form_default_row('', 'Dt Registro:', '<span class="label">'.$expediente['dt_inclusao'].'</span>');
				echo form_default_row('', 'Dt Atualização:', '<span class="label">'.$expediente['dt_alteracao'].'</span>');
				echo form_default_row('dt_alteracao_row', 'Dt Envio Comitê:', '<span class="label">'.$expediente['dt_envio_comite'].'</span>');
				echo form_default_row('', 'Dt Conclusão:', '<span class="label label-success">'.$expediente['dt_conclusao'].'</span>');
				echo form_default_row('', 'Status:', '<span class="label label-warning">'.$expediente['ds_expediente_status'].'</span>');
				echo form_default_row('', 'Descrição:', nl2br($expediente['ds_descricao']));
			echo form_end_box('default_box');
		
			if(trim($expediente['dt_conclusao']) == '')
			{
				echo form_start_box('andamento_box', 'Andamento');
					echo form_default_dropdown_db('cd_expediente_status', 'Status: (*)', array('comite_etica.expediente_status', 'cd_expediente_status', 'ds_expediente_status'), array(), '', '', TRUE,'dropdown_db.dt_exclusao IS NULL AND dropdown_db.cd_expediente_status > 2');
					echo form_default_textarea('ds_expediente_andamento', 'Descrição: (*)', '', 'style="height: 100px;"');
				echo form_end_box('andamento_box');	

				echo form_command_bar_detail_start();
					echo button_save('Salvar');
				echo form_command_bar_detail_end();	
			}
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>