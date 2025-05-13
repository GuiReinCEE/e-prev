<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('ds_projeto_cronograma', 'nr_ordem'), 'form_valida(form)');
	?>
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/projeto') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/projeto/cadastro/'.$row['cd_projeto']) ?>";
	}

	function ir_indicador()
	{
		location.href = "<?= site_url('gestao/projeto/indicador/'.$row['cd_projeto']) ?>";
	}

	function ir_custo()
	{
		location.href = "<?= site_url('gestao/projeto/custo/'.$row['cd_projeto']) ?>";
	}
	
	function ir_cronograma()
	{
		location.href = "<?= site_url('gestao/projeto/cronograma/'.$row['cd_projeto']) ?>";
	}

	function excluir(cd_projeto, cd_projeto_cronograma, cd_projeto_cronograma_pai)
	{
		var confirmacao = 'Você deseja mesmo excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/projeto/excluir_cronograma') ?>"+"/"+cd_projeto+"/"+cd_projeto_cronograma+"/"+cd_projeto_cronograma_pai;
		}
	}
	
	function form_valida(form)
	{
		var fl_marcado = false;

		$("input[type='checkbox'][id='gerencia']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);	

		if(!fl_marcado)
		{
			alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação) \n\n[gerencia]");
			return false;
		}
		else
		{
			if(confirm("Salvar?"))
			{
				form.submit();
			}
		}	
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			null,
		    "CaseInsensitiveString",
			null,
		    "DateBR",
		    "DateBR",
		    "DateBR",
		    "DateBR",
			null,
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

	function alterar_ordem(cd_projeto, cd_projeto_cronograma)
    {
        $("#ajax_ordem_valor_" + cd_projeto_cronograma).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('gestao/projeto/altera_ordem') ?>",
        {
            cd_projeto_cronograma : cd_projeto_cronograma,
            nr_ordem : $("#nr_ordem_" + cd_projeto_cronograma).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_projeto_cronograma).empty();
			
			$("#nr_ordem_" + cd_projeto_cronograma).hide();
			$("#ordem_salvar_" + cd_projeto_cronograma).hide(); 
			
            $("#ordem_valor_" + cd_projeto_cronograma).html($("#nr_ordem_" + cd_projeto_cronograma).val()); 
			$("#ordem_valor_" + cd_projeto_cronograma).show(); 
			$("#ordem_editar_" + cd_projeto_cronograma).show();
        });
    }	
	
	function editar_ordem(cd_projeto_cronograma)
	{
		$("#ordem_valor_" + cd_projeto_cronograma).hide(); 
		$("#ordem_editar_" + cd_projeto_cronograma).hide(); 

		$("#ordem_salvar_" + cd_projeto_cronograma).show(); 
		$("#nr_ordem_" + cd_projeto_cronograma).show(); 
		$("#nr_ordem_" + cd_projeto_cronograma).focus();	
	}
	
	$(function(){
		configure_result_table();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
	$abas[] = array('aba_custo', 'Custos Projetados', FALSE, 'ir_custo();');
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	$abas[] = array('aba_sub_cronograma', 'Sub Cronograma', TRUE, 'location.reload();');

	$head = array( 
		'Ordem',
		'',
		'Etapa',
		'Gerência Responsável',
		'Início Previsto',
		'Fim Previsto',
		'Início Realizado',
		'Fim Realizado',
		'Sub Cronograma',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$link_realizacao = '';
		$link_excluir = '';
		
		if((($this->session->userdata('divisao') == trim($row['cd_gerencia_resposanvel'])) OR ($this->session->userdata('divisao') == 'GC')) AND (($item['dt_projeto_cronograma_ini'] != '') AND ($item['dt_projeto_cronograma_fim'] != '')))
		{
			$link_realizacao = '<br>'.anchor('gestao/projeto/cronograma_realizado/'.$row['cd_projeto'].'/'.$item['cd_projeto_cronograma'], '[Informar Realização]');
		}
		
		if((($this->session->userdata('divisao') == trim($row['cd_gerencia_resposanvel'])) OR ($this->session->userdata('divisao') == 'GC')) AND (!isset($item['sub_cronograma_nome'])) AND (!isset($item['dt_projeto_cronograma_realizado_fim'])))
		{
			$link_excluir = '<br><a href="javascript:void(0);" onclick="excluir('.$row['cd_projeto'].', '.$item['cd_projeto_cronograma'].', '.$item['cd_projeto_cronograma_pai'].');" title="Excluir">[Excluir]</a>';
		}
		
		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_projeto_cronograma'], 
			'id'     => 'nr_ordem_'.$item['cd_projeto_cronograma'],
			'onblur' => 'alterar_ordem('.$item['cd_projeto'].', '.$item['cd_projeto_cronograma'].');',
			'style'  => 'display:none; width:50px;'
		);
	
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_projeto_cronograma'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_projeto_cronograma'].'">'.$item['nr_ordem'].'</span>'.
			form_input($config, $item['nr_ordem']).'
			<script> $(function(){ $("#cd_projeto_cronograma_'.$item['cd_projeto_cronograma'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_projeto_cronograma'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_projeto_cronograma'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_projeto_cronograma'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			($item['dt_projeto_cronograma_realizado_fim'] == '' ? array(anchor('gestao/projeto/sub_cronograma/'.$row['cd_projeto'].'/'.$item['cd_projeto_cronograma_pai'].'/'.$item['cd_projeto_cronograma'], $item['ds_projeto_cronograma']), 'text-align:left;') : array($item['ds_projeto_cronograma'], 'text-align:left;')),
			(isset($item['gerencia_lista']) ? implode(', ', $item['gerencia_lista']) : ''),
			$item['dt_projeto_cronograma_ini'],
			$item['dt_projeto_cronograma_fim'],
			$item['dt_projeto_cronograma_realizado_ini'],
			$item['dt_projeto_cronograma_realizado_fim'],
			(isset($item['sub_cronograma_nome']) ? array(implode('<br/>', $item['sub_cronograma_nome']), 'text-align: left;') : ''),
			anchor('gestao/projeto/sub_cronograma/'.$row['cd_projeto'].'/'.$item['cd_projeto_cronograma'], '[Sub Cronograma]').$link_realizacao.$link_excluir
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/projeto/salvar_cronograma');
			echo form_start_box('default_box', 'Projeto');
				echo form_default_hidden('cd_projeto', '', $row['cd_projeto']);	
				echo form_default_row('ds_projeto', 'Projeto :', $row['ds_projeto'], 'style="width:350px;"');
			echo form_end_box('default_box');
			
			echo form_start_box('default_indicador_box', 'Cronograma');
				echo form_default_hidden('cd_projeto_cronograma_pai', '', $cronograma['cd_projeto_cronograma']);	
				echo form_default_row('ds_projeto_cronograma', 'Etapa :', $cronograma['ds_projeto_cronograma'], 'style="width:350px;"');
				echo (isset($cronograma['dt_projeto_cronograma_ini']) ? form_default_row('dt_projeto_cronograma_ini', 'Início Previsto :', $cronograma['dt_projeto_cronograma_ini']) : '');
				echo (isset($cronograma['dt_projeto_cronograma_fim']) ? form_default_row('dt_projeto_cronograma_fim', 'Fim Previsto :', $cronograma['dt_projeto_cronograma_fim']) : '');
				echo (isset($cronograma_gerencia) ? form_default_row('gerencia', 'Gerência Responsável :', implode(', ', $cronograma_gerencia)) : '');
			echo form_end_box('default_indicador_box');
			
			echo form_start_box('default_sub_cronograma_box', 'Sub Cronograma');
				echo form_default_hidden('cd_projeto_cronograma', '', $sub_cronograma['cd_projeto_cronograma']);	
				echo form_default_integer('nr_ordem', 'Ordem :*', $sub_cronograma);	
				echo form_default_text('ds_projeto_cronograma', 'Etapa :*', $sub_cronograma, 'style="width:350px;"');
				echo form_default_checkbox_group('gerencia', 'Gerência Responsável :*', $gerencia, $sub_cronograma_gerencia, 120);
				echo form_default_date('dt_projeto_cronograma_ini', 'Início Previsto :', $sub_cronograma);
				echo form_default_date('dt_projeto_cronograma_fim', 'Fim Previsto :', $sub_cronograma);
			echo form_end_box('default_sub_cronograma_box');
			
			echo form_command_bar_detail_start();
				if($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel'])
				{
					echo button_save('Salvar');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>