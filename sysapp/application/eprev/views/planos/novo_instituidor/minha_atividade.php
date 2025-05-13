<?php
    set_title('Operacionalização de Novo Instituidor - Atividade');
    $this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_acompanhamento'), 'valida(form);') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('planos/novo_instituidor/minhas') ?>";
	} 

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBR",
		    "CaseInsensitiveString",
		    "Number",
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

	function excluir(cd_novo_instituidor_atividade_acompanhamento)
	{
		var confirmacao = 'Deseja excluir o Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('planos/novo_instituidor/excluir_acompanhamento/'.intval($atividade['cd_novo_instituidor']).'/'.intval($atividade['cd_novo_instituidor_atividade'])) ?>/' + cd_novo_instituidor_atividade_acompanhamento;
		}
	}
	function encerrar()
	{
		var confirmacao = 'Deseja Encerrar a Atividade?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('planos/novo_instituidor/encerrar_acompanhamento/'.intval($atividade['cd_novo_instituidor']).'/'.intval($atividade['cd_novo_instituidor_atividade'])) ?>';
		}
	}

	function cancelar()
	{
		location.href = "<?= site_url('planos/novo_instituidor/minha_atividade/'.intval($atividade['cd_novo_instituidor']).'/'.intval($atividade['cd_novo_instituidor_atividade'])) ?>";
	}

	function valida(form)
	{
		var cd_atividade = $('#cd_atividade').val();

		if(cd_atividade != '')
		{
			$.post("<?= site_url('planos/novo_instituidor/valida_atividade') ?>",
			{
				cd_atividade : cd_atividade
			},
			function(data)
			{
				if($('#cd_atividade').val() != '')
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
				}
				else
				{
					$('form').submit(); 
				}

			}, 'json', true);
		}
		else
		{
			$('form').submit(); 
		}
	}

	$(function(){
		configure_result_table();
	});	
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');

	$head = array(
		'Dt. Inclusão',
		'Descrição',
		'OS',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$link = (trim($atividade['dt_encerramento']) == '' ?'<a href="javascript:void(0)" onclick="excluir('.$item['cd_novo_instituidor_atividade_acompanhamento'].')">[excluir]</a>' : '');

		$body[] = array(
			$item['dt_inclusao'],
			array(anchor('planos/novo_instituidor/minha_atividade/'.intval($atividade['cd_novo_instituidor']).'/'.intval($atividade['cd_novo_instituidor_atividade']).'/'.$item['cd_novo_instituidor_atividade_acompanhamento'], nl2br($item['ds_acompanhamento'])), 'text-align:justify;'),
			anchor('atividade/atividade_solicitacao/index/'.$item['cd_gerencia'].'/'.$item['cd_atividade'], $item['cd_atividade']),
			array($item['ds_usuario_inclusao'], 'text-align:left;'),
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Atividade');
	        echo form_default_row('ds_novo_instituidor_atividade', 'Atividade:', $atividade['ds_novo_instituidor_atividade']);
			echo form_default_textarea('ds_atividade', 'Descrição:', $atividade['ds_atividade'],'style="height:80px;" readonly=""');
			echo form_default_row('dt_prazo', 'Prazo:',  '<label class="label label-'.trim($atividade['ds_class_prazo']).'">'.$atividade['dt_prazo'].'</label>');
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();    
			if(trim($atividade['dt_encerramento']) == '')
			{
				echo button_save('Encerrar', 'encerrar()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();	

		if(trim($atividade['dt_encerramento']) == '')
		{
			echo form_open('planos/novo_instituidor/salvar_acompanhamento');
				echo form_start_box('default_box_acom', 'Acompanhamento');
					echo form_default_hidden('cd_novo_instituidor', '', $atividade['cd_novo_instituidor']);	
		            echo form_default_hidden('cd_novo_instituidor_atividade', '', $atividade['cd_novo_instituidor_atividade']);			
					echo form_default_hidden('cd_novo_instituidor_atividade_acompanhamento', '', $row['cd_novo_instituidor_atividade_acompanhamento']);
					echo form_default_textarea('ds_acompanhamento', 'Descrição: (*)', $row['ds_acompanhamento'], 'style="height:80px;"');
					echo form_default_integer('cd_atividade', 'OS:', $row['cd_atividade']);
				echo form_end_box('default_box_acom');	
				echo form_command_bar_detail_start();
					echo button_save('Salvar');

					if(intval($row['cd_novo_instituidor_atividade_acompanhamento']) > 0)
					{
						echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
					}
				echo form_command_bar_detail_end();	
			echo form_close();	
		}

		echo br();
		echo $grid->render();		
	echo aba_end();
	$this->load->view('footer_interna');
?>