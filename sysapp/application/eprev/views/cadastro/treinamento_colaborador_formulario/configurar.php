<?php
    set_title('Formulário de Treinamento - Configurar');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_treinamento_colaborador_formulario_estrutura_conf', 'ds_treinamento_colaborador_formulario_estrutura_conf', 'fl_campo_adicional')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario') ?>";
    }

	function ir_cadastro()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/cadastro/'.intval($cadastro['cd_treinamento_colaborador_formulario'])) ?>";
    }
	
	function ir_estrutura()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/estrutura/'.intval($cadastro['cd_treinamento_colaborador_formulario'])) ?>";
    }
	<? if(isset($sub_estrutura)): ?>
	function ir_sub_estrutura()
    {
       location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/sub_estrutura/'.intval($cadastro['cd_treinamento_colaborador_formulario']).'/'.intval($estrutura['cd_treinamento_colaborador_formulario_estrutura'])) ?>";
	}
	<? endif; ?>
	
	function cancelar()
	{
		location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/configurar/'.intval($cadastro['cd_treinamento_colaborador_formulario']).'/'.intval($configurar['cd_treinamento_colaborador_formulario_estrutura'])) ?>";
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "Number",
			null,
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
		ob_resul.sort(0, false);
	}
	
	function alterar_ordem(cd_treinamento_colaborador_formulario_estrutura, cd_treinamento_colaborador_formulario_estrutura_conf)
    {
        $("#ajax_ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura_conf).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('cadastro/treinamento_colaborador_formulario/altera_ordem_configurar') ?>",
        {
            cd_treinamento_colaborador_formulario_estrutura_conf : cd_treinamento_colaborador_formulario_estrutura_conf,
            nr_treinamento_colaborador_formulario_estrutura_conf : $("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura_conf).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura_conf).empty();
			
			$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura_conf).hide();
			$("#ordem_salvar_" + cd_treinamento_colaborador_formulario_estrutura_conf).hide(); 
			
            $("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura_conf).html($("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura_conf).val()); 
			$("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura_conf).show(); 
			$("#ordem_editar_" + cd_treinamento_colaborador_formulario_estrutura_conf).show();
        });
    }	
	
	function editar_ordem(cd_treinamento_colaborador_formulario_estrutura_conf)
	{
		$("#ordem_valor_" + cd_treinamento_colaborador_formulario_estrutura_conf).hide(); 
		$("#ordem_editar_" + cd_treinamento_colaborador_formulario_estrutura_conf).hide(); 

		$("#ordem_salvar_" + cd_treinamento_colaborador_formulario_estrutura_conf).show(); 
		$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura_conf).show(); 
		$("#nr_ordem_" + cd_treinamento_colaborador_formulario_estrutura_conf).focus();	
	}
		
	$(function(){
		configure_result_table();
	});

</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
	
	if(isset($sub_estrutura))
	{
		$abas[] = array('aba_sub_estrutura', 'Sub Estrutura', FALSE, 'ir_sub_estrutura();');
	}
	
	$abas[] = array('aba_configurar', 'Configurar', TRUE, 'location.reload();');
	
	$head = array(
		'Ordem',
		'',
		'Descrição',
		'Abrir campo adicional'
	);

	$body = array();
	
	foreach ($collection as $item)
	{	
		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'], 
			'id'     => 'nr_ordem_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'],
			'onblur' => 'alterar_ordem('.$item['cd_treinamento_colaborador_formulario_estrutura'].', '.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].');',
			'style'  => 'display:none; width:50px;'
		);
	
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].'">'.$item['nr_treinamento_colaborador_formulario_estrutura_conf'].'</span>'.
			form_input($config, $item['nr_treinamento_colaborador_formulario_estrutura_conf']).'
			<script> $(function(){ $("#cd_treinamento_colaborador_formulario_estrutura_conf_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			array(anchor('cadastro/treinamento_colaborador_formulario/configurar/'.$item['cd_treinamento_colaborador_formulario'].'/'.$item['cd_treinamento_colaborador_formulario_estrutura'].'/'.$item['cd_treinamento_colaborador_formulario_estrutura_conf'], $item['ds_treinamento_colaborador_formulario_estrutura_conf']), 'text-align: left;'),			
			$item['campo_adicional']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
		
    echo aba_start($abas);
        echo form_open('cadastro/treinamento_colaborador_formulario/configurar_salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_row('ds_treinamento_colaborador_formulario', 'Formulário:', $cadastro['ds_treinamento_colaborador_formulario'], 'style="width:300px;"');
				echo form_default_row('cd_treinamento_colaborador_tipo', 'Tipo Treinamento:',  nl2br(implode(', ', $cadastro['tipo'])));
				echo form_default_row('enviar_para', 'Respondente:', $cadastro['enviar_para']);
			echo form_end_box('default_box'); 
			echo form_start_box('default_estrutura_box', 'Estrutura');
				echo form_default_hidden('cd_treinamento_colaborador_formulario', '', $cadastro['cd_treinamento_colaborador_formulario']);  
				echo form_default_hidden('cd_treinamento_colaborador_formulario_estrutura', '', $estrutura['cd_treinamento_colaborador_formulario_estrutura']);
				echo form_default_row('ds_treinamento_colaborador_formulario_estrutura_tipo', 'Tipo:', $estrutura['ds_treinamento_colaborador_formulario_estrutura_tipo']);
				echo form_default_row('ds_treinamento_colaborador_formulario_estrutura', 'Descrição:', $estrutura['nr_treinamento_colaborador_formulario_estrutura'].') '.$estrutura['ds_treinamento_colaborador_formulario_estrutura']);
				if(isset($sub_estrutura))
				{
					echo form_default_row('ds_treinamento_colaborador_formulario_estrutura', 'Sub Estrutura:', $sub_estrutura['nr_treinamento_colaborador_formulario_estrutura'].') '.$sub_estrutura['ds_treinamento_colaborador_formulario_estrutura']);
				}
			echo form_end_box('default_estrutura_box'); 
			echo form_start_box('default_configurar_box', 'Configurar');
			 	echo form_default_hidden('cd_treinamento_colaborador_formulario_estrutura', '', $configurar['cd_treinamento_colaborador_formulario_estrutura']);  
				echo form_default_hidden('cd_treinamento_colaborador_formulario_estrutura_conf', '', $configurar['cd_treinamento_colaborador_formulario_estrutura_conf']);
				echo form_default_text('nr_treinamento_colaborador_formulario_estrutura_conf', 'Ordem: (*)', $configurar);
				echo form_default_text('ds_treinamento_colaborador_formulario_estrutura_conf', 'Descrição: (*)', $configurar, 'style="width:300px;"');
				if(intval($estrutura['cd_treinamento_colaborador_formulario_estrutura_tipo']) == 3)
				{
					echo form_default_dropdown('fl_campo_adicional', 'Abrir campo adicional: (*)', $campo_adicional, $configurar['fl_campo_adicional']);
				}
			echo form_end_box('default_configurar_box'); 
			echo form_command_bar_detail_start();   
				echo button_save('Salvar');
				if(intval($configurar['cd_treinamento_colaborador_formulario_estrutura_conf']) > 0)
				{
					echo button_save("Cancelar", 'cancelar()', 'botao_disabled');
				}
			echo form_command_bar_detail_end();
        echo form_close();

		echo $grid->render();
		
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');	
?>