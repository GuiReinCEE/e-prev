<?php
	set_title('Sistema e-prev - Método');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('nr_ordem', 'ds_sistema_metodo'), 'valida(form);') ?>

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

	function ir_atividade()
	{
		location.href = "<?= site_url("servico/sistema_eprev/atividade/".intval($sistema["cd_sistema"])) ?>";
	}

	function ir_pendencia()
	{
		location.href = "<?= site_url("servico/sistema_eprev/pendencia/".intval($sistema["cd_sistema"])) ?>";
	}
	
	function excluir(cd_sistema_metodo)
	{
		var confirmacao = 'Deseja excluir o Método?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/sistema_eprev/excluir_metodo/'.$sistema['cd_sistema']) ?>/" + cd_sistema_metodo;
		}
	}

	function valida(form)
	{
		$.post("<?= site_url('servico/sistema_eprev/valida_evento') ?>",
		{
			cd_evento : $('#cd_evento').val()
		},
		function(data)
		{
			if(data['valida'] == 0)
			{
				alert('Número de evento não existe');
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
			"CaseInsensitiveString",
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
		ob_resul.sort(0, false);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_metodo', 'Método',TRUE, 'location.reload();');
	$abas[] = array('aba_rotina', 'Rotina', FALSE, 'ir_rotina();');
	$abas[] = array('aba_pendencia', 'Pendências', FALSE , 'ir_pendencia();');
	$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');	
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	
	
	$head = array(
		'Ordem',
		'Método',
		'Descrição',
		'Evento',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = '';

		if(intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo'))
		{
			$link = '<a href="javascript:void(0)" onclick="excluir('.$item['cd_sistema_metodo'].')">[excluir]</a>';
		}

		$body[] = array(
			array(anchor('servico/sistema_eprev/metodo/'.$item['cd_sistema'],$item['nr_ordem']), 'text-align:left;'),
			anchor('servico/sistema_eprev/metodo/'.$item['cd_sistema'].'/'.$item['cd_sistema_metodo'], $item['ds_sistema_metodo']),
			array(nl2br($item['ds_descricao']), 'text-align:justify;'),
			array($item['ds_evento'],'text-align:left;'),
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/metodo_salvar');
			echo form_start_box('default_sistema_box', 'Sistema');
				echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
				echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
				echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);		
				echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);			
			echo form_end_box('default_sistema_box');

			echo form_start_box('default_box', 'Cadastro');
				echo form_default_hidden('cd_sistema_metodo', '', $row['cd_sistema_metodo']);
				echo form_default_integer('nr_ordem', 'Ordem: (*)', $row['nr_ordem']);
				echo form_default_text('ds_sistema_metodo', 'Método: (*)', $row['ds_sistema_metodo'], 'style="width:350px;"');
				echo form_default_textarea('ds_descricao', 'Descrição: ', $row['ds_descricao'], 'style="height:100px;"');
				echo form_default_integer('cd_evento', 'Cód. Evento: ', $row['cd_evento']);
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