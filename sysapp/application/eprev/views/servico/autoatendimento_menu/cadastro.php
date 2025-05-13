<?php
	set_title('Menu Autoatendimento - Cadastro');
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
	
	function ir_sub_menu()
    {
        location.href = "<?= site_url('servico/autoatendimento_menu/sub_menu/'.intval($row['cd_menu'])) ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	if(intval($row['cd_menu']) > 0)
	{
		$abas[] = array('aba_sub_menu', 'Sub Menu', FALSE, 'ir_sub_menu();');
	}
    
	echo aba_start( $abas );
        echo form_open('servico/autoatendimento_menu/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_menu', '', $row['cd_menu']);
				echo form_default_text('ds_codigo', 'Cód: (*)', $row, 'style="width:300px;"');
				echo form_default_text('ds_menu', 'Menu: (*)', $row, 'style="width:300px;"');
				echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
				echo form_default_dropdown('fl_status', 'Status: (*)', $status, $row['fl_status']);
				echo form_default_text('ds_href', 'Link:', $row, 'style="width:300px;"');
				echo form_default_text('ds_icone', 'Ícone:', $row, 'style="width:300px;"');
				echo form_default_checkbox_group('empresa', 'Empresa:', $empresa, $menu_patrocinadoras, 120);
				echo form_default_checkbox_group('tipo_participante', 'Tipo Participante:', $tipo_participante, $menu_tipo_participante, 120);
				echo form_default_textarea('ds_resumo', 'Descrição:', $row);
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>