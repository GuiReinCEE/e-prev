<?php
	set_title('Sistema e-prev - Atividade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_atividade'), 'valida(form);') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('servico/sistema_eprev/index') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('servico/sistema_eprev/cadastro/'.$sistema['cd_sistema'])?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('servico/sistema_eprev/acompanhamento/'.intval($sistema['cd_sistema']))?>";
	}

	function ir_anexo()
	{
		location.href = '<?= site_url('servico/sistema_eprev/anexo/'.$sistema['cd_sistema']) ?>';
	}

	function ir_rotina()
	{
		location.href = "<?= site_url('servico/sistema_eprev/rotina/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_metodo()
	{
		location.href = "<?= site_url("servico/sistema_eprev/metodo/".intval($sistema["cd_sistema"])) ?>";
	}

	function ir_pendencia()
	{
		location.href = "<?= site_url('servico/sistema_eprev/pendencia/'.intval($sistema['cd_sistema'])) ?>";
	}

	function excluir(cd_sistema_atividade)
	{
		var confirmacao = 'Deseja excluir o Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/sistema_eprev/excluir_atividade/'.$sistema['cd_sistema']) ?>/" + cd_sistema_atividade;
		}
	}

	function valida(form)
	{
		$.post("<?= site_url('servico/sistema_eprev/valida_atividade') ?>",
		{
			cd_atividade : $('#cd_atividade').val()
		},
		function(data)
		{
			if(data['valida'] == 0)
			{
				alert('Número de atividade não existe');
				return false;
			}
			else
			{
				$('form').submit(); 
			}

		}, 'json', true);
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"DateBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
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
	$abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');	
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	 

	$head = array(
		'Nº Atividade',
		'Data',
		'Solicitante',
		'Atendente',
		'Descrição',
		'Status',
		'Dt. Conclusão',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = '';

		if(intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo'))
		{
			$link = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_sistema_atividade'].')">[excluir]</a>';
		}

		$body[] = array(
			anchor('atividade/atividade_solicitacao/index/'.$item['area'].'/'.$item['cd_atividade'], $item['cd_atividade']),
			$item['dt_cad'],
			array($item['ds_solicitante'], 'text-align:left;'), 
			array($item['ds_atendente'], 'text-align:left;'),
			array(nl2br($item['descricao']), 'text-align:justify;'),
			'<span class="'.$item['status_label'].'">'.$item['ds_status'].'</span>',
			$item['dt_conclusao'],
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/salvar_atividade');
			echo form_start_box('default_sistema_box', 'Sistema');
				echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
				echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
				echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);		
				echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);			
			echo form_end_box('default_sistema_box');

			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_sistema_atividade', '', $row['cd_sistema_atividade']);
				echo form_default_integer('cd_atividade', 'Nº Atividade: (*)', $row['cd_atividade']);
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();     
	            echo button_save('Salvar');
	        echo form_command_bar_detail_end();
			
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>