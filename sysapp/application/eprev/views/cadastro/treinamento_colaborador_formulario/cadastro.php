<?php
    set_title('Formulário de Treinamento - Cadastro');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_treinamento_colaborador_formulario', 'fl_enviar_para', 'nr_dias_envio'), 'valida_tipo(form);') ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario') ?>";
    }

    function valida_tipo(form)
    {
        var fl_marcado = false;

        $("input[type='checkbox'][id='tipo']").each( 
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
            alert("Informe algum tipo de treinamento.");
            return false;
        }
        else
        {
            form.submit();
        }
    }

	function ir_estrutura()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario/estrutura/'.intval($row['cd_treinamento_colaborador_formulario'])) ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	if(intval($row['cd_treinamento_colaborador_formulario']) > 0)
	{
		$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
	}

    echo aba_start($abas);
        echo form_open('cadastro/treinamento_colaborador_formulario/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_treinamento_colaborador_formulario', '', $row['cd_treinamento_colaborador_formulario']);
				echo form_default_text('ds_treinamento_colaborador_formulario', 'Formulário: (*)', $row, 'style="width:300px;"');
                echo form_default_dropdown('fl_enviar_para', 'Respondente: (*)', $respondente, $row['fl_enviar_para']);
                echo form_default_checkbox_group('tipo', 'Tipo Treinamento:(*)', $tipo, $tipo_checked, 200);
				echo form_default_integer('nr_dias_envio', 'Dias para Envio: (*)', $row);
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>