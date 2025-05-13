<?php
	set_title('Sistema e-prev - Acompanhamento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_acompanhamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('servico/sistema_eprev/index') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('servico/sistema_eprev/cadastro/'.$sistema['cd_sistema'])?>";
	}

	function excluir(cd_sistema_acompanhamento)
	{
		var confirmacao = 'Deseja excluir o Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/sistema_eprev/excluir_acompanhamento/'.$sistema['cd_sistema']) ?>/" + cd_sistema_acompanhamento;
		}
	}

	function ir_atividade()
	{
		location.href = "<?= site_url('servico/sistema_eprev/atividade/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_rotina()
	{
		location.href = "<?= site_url('servico/sistema_eprev/rotina/'.intval($sistema['cd_sistema'])) ?>";
	}

    function cancelar()
	{
		location.href = "<?= site_url('servico/sistema_eprev/acompanhamento/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_metodo()
	{
		location.href = "<?= site_url('servico/sistema_eprev/metodo/'.intval($sistema['cd_sistema'])) ?>";
	}
             
	function ir_pendencia()
	{
		location.href = "<?= site_url('servico/sistema_eprev/pendencia/'.intval($sistema['cd_sistema'])) ?>";
	}
	
	function ir_anexo()
	{
		location.href = '<?= site_url('servico/sistema_eprev/anexo/'.$sistema['cd_sistema']) ?>';
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateTimesBR",
			"CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}

	function novo()
	{
		location.href = "<?= site_url('servico/sistema_eprev/cadastro') ?>";
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_metodo', 'Método',FALSE, 'ir_metodo();');
	$abas[] = array('aba_rotina', 'Rotina', FALSE, 'ir_rotina();');
	$abas[] = array('aba_pendencia', 'Pendências', FALSE, 'ir_pendencia();');
	$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');	
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	
	$head = array(
		'Dt. Inclusão',
		'Descrição',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			anchor('servico/sistema_eprev/acompanhamento/'.$sistema['cd_sistema'].'/'.$item['cd_sistema_acompanhamento'], $item['dt_inclusao']),
			array(nl2br($item['ds_acompanhamento']), 'text-align:justify;'),
			array($item['ds_usuario_inclusao'], 'text-align:left;'), 
			'<a href="javascript:void(0)" onclick="excluir('.$item['cd_sistema_acompanhamento'].')">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/salvar_acompanhamento');
			echo form_start_box('default_sistema_box', 'Sistema');
				echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
				echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
				echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);		
				echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);			
			echo form_end_box('default_sistema_box');

			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_sistema_acompanhamento', '', $row['cd_sistema_acompanhamento']);
				echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', $row['ds_acompanhamento'], 'style="height:150px;"');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();     
	            echo button_save('Salvar');
	            if(intval($row['cd_sistema_acompanhamento']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
				}
			echo form_command_bar_detail_end();
			
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>