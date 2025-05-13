<?php
	set_title('Menu Autoatendimento - Sub Menu');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_codigo', 'ds_menu', 'nr_ordem', 'fl_status'), 'valida(form);') ?>
   
    function valida(form)
    {
		var fl_marcado_empresa = false;
		var fl_marcado_tipo_participante = false;

		$("input[type='checkbox'][id='empresa']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado_empresa = true;
				} 
			}
		);	

		$("input[type='checkbox'][id='tipo_participante']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado_tipo_participante = true;
				} 
			}
		);				
				
		if(!fl_marcado_empresa)
		{
			alert("Informe a(s) Empresa(s)");
			return false;
		}
		else if(!fl_marcado_tipo_participante)
		{
			alert("Informe o(s) Tipo(s) de Participante");
			return false;
		}
        else
        {
			form.submit();
        }
    }

    function ir_lista()
    {
        location.href = "<?= site_url('servico/autoatendimento_menu') ?>";
    }

	function ir_cadastro()
    {
        location.href = "<?= site_url('servico/autoatendimento_menu/cadastro/'.intval($cadastro['cd_menu'])) ?>";
    }
	
	function cancelar()
	{
		location.href = "<?= site_url('servico/autoatendimento_menu/sub_menu/'.intval($cadastro['cd_menu'])) ?>";
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
	
	function alterar_ordem(cd_menu)
    {
        $("#ajax_ordem_valor_" + cd_menu).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('servico/autoatendimento_menu/altera_ordem') ?>",
        {
            cd_menu : cd_menu,
            nr_ordem : $("#nr_ordem_" + cd_menu).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_menu).empty();
			
			$("#nr_ordem_" + cd_menu).hide();
			$("#ordem_salvar_" + cd_menu).hide(); 
			
            $("#ordem_valor_" + cd_menu).html($("#nr_ordem_" + cd_menu).val()); 
			$("#ordem_valor_" + cd_menu).show(); 
			$("#ordem_editar_" + cd_menu).show();
        });
    }	
	
	function editar_ordem(cd_menu)
	{
		$("#ordem_valor_" + cd_menu).hide(); 
		$("#ordem_editar_" + cd_menu).hide(); 

		$("#ordem_salvar_" + cd_menu).show(); 
		$("#nr_ordem_" + cd_menu).show(); 
		$("#nr_ordem_" + cd_menu).focus();	
	}
	
	$(function(){
		configure_result_table();
	});

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_sub_menu', 'Sub Menu', TRUE, 'location.reload();');
	
	$head = array(
		'Ordem',
		'',
		'Cód',
		'Menu',
		'Status',
		'Empresa',
		'Tipo Participante',
		'Descrição'
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$config = array(
			'name'   => 'nr_ordem_'.$item['cd_menu'], 
			'id'     => 'nr_ordem_'.$item['cd_menu'],
			'onblur' => 'alterar_ordem('.$item['cd_menu'].');',
			'style'  => 'display:none;'
		);
		
		$body[] = array(
			'<span id="ajax_ordem_valor_'.$item['cd_menu'].'"></span> '.
			'<span id="ordem_valor_'.$item['cd_menu'].'">'.$item['nr_ordem'].'</span>'.
			form_input($config, $item['nr_ordem']).'
			<script> $(function(){ $("#cd_menu_'.$item['cd_menu'].'").numeric(); }); </script>',
			'<a id="ordem_editar_'.$item['cd_menu'].'" href="javascript:void(0);" onclick="editar_ordem('.$item['cd_menu'].');" title="Editar a Ordem">[editar ordem]</a>'.
			'<a id="ordem_salvar_'.$item['cd_menu'].'" href="javascript:void(0);" style="display:none;" title="Salvar a Ordem">[salvar]</a>',
			anchor('servico/autoatendimento_menu/sub_menu/'.$item['cd_menu_pai'].'/'.$item['cd_menu'], $item['ds_codigo']),
			array($item['ds_menu'], 'text-align: left;'),
			($item['status'] == 'Ativo' ? '<span class = "label label-success">'.$item['status'].'</span>' : '<span class = "label label-important">'.$item['status'].'</span>'),
			array(nl2br(implode(br(), $item['tipo_empresa'])), 'text-align: left;'),
			array(nl2br(implode(br(), $item['tipo_participante'])), 'text-align: left;'),
			array(nl2br($item['ds_resumo']), 'text-align: justify;')
		);	
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
    echo aba_start($abas);
        echo form_open('servico/autoatendimento_menu/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_row('ds_menu', 'Menu:', $cadastro['ds_menu'], 'style="width:300px;"');
			echo form_end_box('default_box'); 
			echo form_start_box('default_sub_menu_box', 'Sub Menu');
				echo form_default_hidden('cd_menu_pai', '', $cadastro['cd_menu']);
				echo form_default_hidden('cd_menu', '', $sub_menu['cd_menu']);
				echo form_default_text('ds_codigo', 'Cód: (*)', $sub_menu, 'style="width:300px;"');
				echo form_default_text('ds_menu', 'Menu: (*)', $sub_menu, 'style="width:300px;"');
				echo form_default_integer('nr_ordem', 'Ordem: (*)', $sub_menu);
				echo form_default_dropdown('fl_status', 'Status: (*)', $status, $sub_menu['fl_status']);
				echo form_default_text('ds_href', 'Link:', $sub_menu, 'style="width:300px;"');
				echo form_default_text('ds_icone', 'Ícone:', $sub_menu, 'style="width:300px;"');
				echo form_default_checkbox_group('empresa', 'Empresa:', $empresa, $menu_patrocinadoras, 120);
				echo form_default_checkbox_group('tipo_participante', 'Tipo Participante:', $tipo_participante, $menu_tipo_participante, 120);
				echo form_default_textarea('ds_resumo', 'Descrição:', $sub_menu);
			echo form_end_box('default_sub_menu_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
				if(intval($sub_menu['cd_menu_pai']) > 0)
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