<?php
	set_title('Família Vendas');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_app_solicitacao_acompanhamento')); ?>

	function ir_lista()
	{
		 location.href = "<?= site_url('ecrm/familiavendas_solicitacao') ?>";
	}

	function ir_cadastro()
	{
		 location.href = "<?= site_url('ecrm/familiavendas_solicitacao/cadastro/'.$row['cd_app_solicitacao']) ?>";
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(1, true);
	}

	$(function(){
		configure_result_table()
	});
</script>
<?php

	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');	

	echo aba_start($abas);
		echo form_open();
			echo form_start_box('default_box', '');
				echo form_default_row('nr_protocolo','Nr. Protocolo:', '<span class="label label-inverse">'.$row['nr_protocolo'].'</span>');
				echo form_default_row('dt_alteracao', 'Dt. Solicitação:', $row['dt_alteracao'], 'style="width:400px;"');
				echo form_default_row('ds_status', 'Status:', '<span class="'.$row['ds_class_status'].'">'.$row['ds_status'].'</span>','style="width:400px;"');
				echo form_default_row('ds_nome', 'Nome:', $row['ds_nome'],'style="width:400px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
            echo form_command_bar_detail_end();
		echo form_close();

		echo form_open('ecrm/familiavendas_solicitacao/salvar_acompanhamento');
			echo form_start_box('default_box', 'Acompanhamento');
				echo form_default_hidden('cd_app_solicitacao','', $row['cd_app_solicitacao']);
				echo form_default_textarea('ds_app_solicitacao_acompanhamento','Descrição:','');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
            echo form_command_bar_detail_end();
		echo form_close();

	$head = array( 
		'Descrição',
		'Dt. Inclusão',
		'Usuário'
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			array(anchor('ecrm/familiavendas_solicitacao/acompanhamento/'.$row['cd_app_solicitacao'], $item['ds_app_solicitacao_acompanhamento']), 'text-align:left;'),
			$item['dt_alteracao'],
			$item['ds_usuario_inclusao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
	echo aba_end();

	$this->load->view('footer');
?>