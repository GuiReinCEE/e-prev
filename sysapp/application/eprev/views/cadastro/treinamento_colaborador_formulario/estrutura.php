<?php
	set_title('Formulário de Treinamento - Estrutura');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_treinamento_colaborador_formulario_estrutura', 'cd_treinamento_colaborador_formulario_estrutura_tipo', 'ds_treinamento_colaborador_formulario_estrutura', 'fl_obrigatorio')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario') ?>";
    }

	function ir_cadastro()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/cadastro/'.intval($cadastro['cd_treinamento_colaborador_formulario'])) ?>";
    }
	
	function cancelar()
	{
		location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/estrutura/'.intval($cadastro['cd_treinamento_colaborador_formulario'])) ?>";
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "Number",
			null,
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
	
	function alterar_ordem(cd_treinamento_colaborador_formulario, cd_treinamento_colaborador_formulario_estrutura)
    {
        $("#ajax_ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('cadastro/treinamento_colaborador_formulario/altera_ordem') ?>",
        {
            cd_treinamento_colaborador_formulario_estrutura : cd_treinamento_colaborador_formulario_estrutura,
            nr_treinamento_colaborador_formulario_estrutura : $("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura).empty();
			
			$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura).hide();
			$("#ordem_salvar_" + cd_treinamento_colaborador_formulario_estrutura).hide(); 
			
            $("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura).html($("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura).val()); 
			$("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura).show(); 
			$("#ordem_editar_" + cd_treinamento_colaborador_formulario_estrutura).show();
        });
    }	
	
	function editar_ordem(cd_treinamento_colaborador_formulario_estrutura)
	{
		$("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura).hide(); 
		$("#ordem_editar_" + cd_treinamento_colaborador_formulario_estrutura).hide(); 

		$("#ordem_salvar_" + cd_treinamento_colaborador_formulario_estrutura).show(); 
		$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura).show(); 
		$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura).focus();	
	}
	
	$(function(){
		configure_result_table();
	});

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_estrutura', 'Estrutura', TRUE, 'location.reload();');
	
	$head = array(
		'Ordem',
		'',
		'Tipo',
		'Descrição',
		'Obrigatório',
		'Sub Estrutura',
		''
	);

	$body = array();
	
	foreach ($collection as $item)
	{	
		$configurar    = '';
		$sub_estrutura = '';
		
		if(((intval($item['cd_treinamento_colaborador_formulario_estrutura_tipo']) == 2) OR (intval($item['cd_treinamento_colaborador_formulario_estrutura_tipo']) == 3)) AND (count($item['sub_estrutura']) == 0))
		{
			$configurar = anchor('cadastro/treinamento_colaborador_formulario/configurar/'.$item['cd_treinamento_colaborador_formulario'].'/'.$item['cd_treinamento_colaborador_formulario_estrutura'], '[configurar]');		
		}
		
		if((intval($item['cd_treinamento_colaborador_formulario_estrutura_tipo']) == 2) AND ($item['count'] == 0))
		{
			$sub_estrutura = anchor('cadastro/treinamento_colaborador_formulario/sub_estrutura/'.$item['cd_treinamento_colaborador_formulario'].'/'.$item['cd_treinamento_colaborador_formulario_estrutura'], '[sub-estrutura]');
		}

		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_treinamento_colaborador_formulario_estrutura'], 
			'id'     => 'nr_ordem_'.$item['cd_treinamento_colaborador_formulario_estrutura'],
			'onblur' => 'alterar_ordem('.$item['cd_treinamento_colaborador_formulario'].', '.$item['cd_treinamento_colaborador_formulario_estrutura'].');',
			'style'  => 'display:none; width:50px;'
		);
	
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_treinamento_colaborador_formulario_estrutura'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_treinamento_colaborador_formulario_estrutura'].'">'.$item['nr_treinamento_colaborador_formulario_estrutura'].'</span>'.
			form_input($config, $item['nr_treinamento_colaborador_formulario_estrutura']).'
			<script> $(function(){ $("#cd_treinamento_colaborador_formulario_estrutura_'.$item['cd_treinamento_colaborador_formulario_estrutura'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_treinamento_colaborador_formulario_estrutura'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_treinamento_colaborador_formulario_estrutura'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_treinamento_colaborador_formulario_estrutura'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			'<span class="'.$item['ds_class'].'">'.$item['ds_treinamento_colaborador_formulario_estrutura_tipo'].'</span>',
			array(nl2br(anchor('cadastro/treinamento_colaborador_formulario/estrutura/'.$item['cd_treinamento_colaborador_formulario'].'/'.$item['cd_treinamento_colaborador_formulario_estrutura'], $item['ds_treinamento_colaborador_formulario_estrutura'])), 'text-align: justify;'),
			$item['obrigatorio'],
			array(nl2br(implode(br(), $item['sub_estrutura'])), 'text-align: justify;'),
		    $configurar.' '.$sub_estrutura
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
    echo aba_start($abas);
        echo form_open('cadastro/treinamento_colaborador_formulario/estrutura_salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_row('ds_treinamento_colaborador_formulario', 'Formulário:', $cadastro['ds_treinamento_colaborador_formulario'], 'style="width:300px;"');
				echo form_default_row('cd_treinamento_colaborador_tipo', 'Tipo Treinamento:', nl2br(implode(', ', $cadastro['tipo'])));
				echo form_default_row('enviar_para', 'Respondente:', $cadastro['enviar_para']);
			echo form_end_box('default_box'); 
			echo form_start_box('default_estrutura_box', 'Estrutura');
				echo form_default_hidden('cd_treinamento_colaborador_formulario', '', $cadastro['cd_treinamento_colaborador_formulario']);  
				echo form_default_hidden('cd_treinamento_colaborador_formulario_estrutura', '', $estrutura['cd_treinamento_colaborador_formulario_estrutura']);
				echo form_default_text('nr_treinamento_colaborador_formulario_estrutura', 'Ordem da Estrutura: (*)', $estrutura);
				echo form_default_dropdown('cd_treinamento_colaborador_formulario_estrutura_tipo', 'Tipo: (*)', $tipo, $estrutura['cd_treinamento_colaborador_formulario_estrutura_tipo']);
				echo form_default_textarea('ds_treinamento_colaborador_formulario_estrutura', 'Descrição: (*)',  $estrutura);
				echo form_default_dropdown('fl_obrigatorio', 'Obrigatório: (*)', $obrigatorio, $estrutura['fl_obrigatorio']);
			echo form_end_box('default_estrutura_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
				if(intval($estrutura['cd_treinamento_colaborador_formulario_estrutura']) > 0)
				{
					echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
				}
			echo form_command_bar_detail_end();
        echo form_close();
		
		echo $grid->render();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');	
?>