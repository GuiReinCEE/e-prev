<?php
    set_title('Pós-Venda - Resposta Participante');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia'), 'valida_form(form)') ?>

    function enviar_email()
	{
        var confirmacao = "Deseja realmente enviar o Pós-Venda?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

		if(confirm(confirmacao))
		{
            location.href = "<?= site_url('ecrm/posvenda/enviar_email/'.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']) ?>";
		}
	}  
    
    function iniciar()
	{
        var confirmacao = "Deseja realmente inicar o Pós-Venda?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/posvenda/iniciar_posvenda/'.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'].'/'.$cd_atendimento) ?>";
        }
	}  
    
    function exibe_complemento(cd_pergunta) 
    { 
        var $form = $('#form_pergunta :input');
      
        $form.each(function(i, $campo){         
            $campo = $(this);
            
            if($campo.attr('name') == cd_pergunta+"[]")
            {
                if(($campo.is(':checked')) && ($('#E_'+$campo.val()).val() == "S"))
                { 
                    $('#C_'+$campo.val()).show();
                    
                    if($('#O_'+$campo.val()).val() == "S")
                    {
                        $('#A_'+$campo.val()).show();
                    }
                }
                else
                {
                    $('#C_'+$campo.val()).hide();
                    $('#A_'+$campo.val()).hide();
                    $('#C_'+$campo.val()).val('');
                }
            }
        });
    } 
    
    function valida_form(form)
    {
        var retorno = true;
        
        var $form = $('#form_pergunta :input');
        
        $form.each(function(i, $campo){  
            $campo = $(this);
           
            if(($campo.is(':checked')) && ($('#E_'+$campo.val()).val() == "S") && ($('#O_'+$campo.val()).val() == "S") && ($('#C_'+$campo.val()).val() == ""))
            {
                
                alert("O preenchimento do complemento é obrigatório.");
                $('#C_'+$campo.val()).focus()
                retorno =  false;
            }
        });		
        
        if(retorno)
        {
            form.submit();
        }

        return retorno;
    }
    
    function encerrar(form)
    {
        var old_name = '';
        var valid    = true;
        var retorno  = true;
        
        var $form = $('#form_pergunta :input');
        
        $form.each(function(i, $campo){  
            $campo = $(this);

            if ($campo.get(0).tagName == "INPUT")
            {
                if($campo.attr('name').toString().indexOf('R_') > -1)
                {          
                    if($campo.attr('name') != old_name)
                    {
                        if( ! valid )
                        {
                            alert('Atenção\n\nVocê deve preencher todos os campos antes de encerrar.');
                            retorno = false;
                            return false;
                        }

                        old_name = $campo.attr('name');
                        valid = false;
                    }
                    
                    if( ! valid && $campo.is(':checked') )
                    {
                        valid = true;
                    }
                }
            }
        });
        
        if(retorno)
        {
            $form.each(function(i, $campo){  
                $campo = $(this);

                if(($campo.is(':checked')) && ($('#E_'+$campo.val()).val() == "S") && ($('#O_'+$campo.val()).val() == "S") && ($('#C_'+$campo.val()).val() == ""))
                {
                    alert("O preenchimento do complemento é obrigatório.");
                    $('#C_'+$campo.val()).focus()
                    retorno =  false;
                }
            });	
        }

        if(retorno)
        {
            var confirmacao = "Deseja realmente encerrar o Pós-Venda?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {
                $("#fl_encerra").val("S");
            
                form.submit();
            }
        }

        return retorno;
    }	
    
    function ir_relatorio_email()
    {
        location.href = "<?= site_url('ecrm/posvenda/relatorio_email') ?>";
    }
    
    function ir_relatorio()
    {
        location.href = "<?= site_url('ecrm/posvenda/relatorio') ?>";
    }
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/posvenda') ?>";
    }
</script>
<?php

    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_envia_email', 'Emails', FALSE, 'ir_relatorio_email();');
    $abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');
    $abas[] = array('aba_iniciar', 'Participante', TRUE, "location.reload();");

    echo aba_start($abas);
        echo form_open();
            echo form_start_box('default_box', 'Participante');
                echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
                echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
                echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
                echo form_default_hidden('cd_atendimento', '', $cd_atendimento);
                echo form_default_hidden('cd_pos_venda_participante', '', $cd_pos_venda_participante);
                echo form_default_row('nome', 'Nome :', $row['nome']);
                echo form_default_row('re', 'RE :', $row['re']);

                if(trim($row['dt_ultimo']) != '')
                {
                   echo form_default_row('dt_ultimo', 'Realizou Pós-Venda em :', '<span class="label label-important">'.trim($row['dt_ultimo']).'</span>');
                }
                else
                {
                    echo form_default_row('dt_ultimo', '', 'Nunca realizou Pós-Venda');
                }
            echo form_end_box('default_box');
            echo form_command_bar_detail_start(); 

                if(trim($row['dt_ultimo']) == '' AND !$fl_iniciar)
                {
                    echo button_save('Enviar por Email', 'enviar_email();');
                }

                if($fl_iniciar)
                {
                    echo button_save('Iniciar', 'iniciar();', 'botao_verde');
                }
            echo form_command_bar_detail_end();
        echo form_close();

        if(count($collection) > 0)
        {
            echo form_open(site_url('ecrm/posvenda/salvar_respostas'), 'name="form_pergunta" id="form_pergunta"');
                echo form_default_hidden('cd_empresa', '', $row['cd_empresa']);
                echo form_default_hidden('cd_registro_empregado', '', $row['cd_registro_empregado']);
                echo form_default_hidden('seq_dependencia', '', $row['seq_dependencia']);
                echo form_default_hidden('cd_pos_venda_participante', '', $cd_pos_venda_participante);
                echo form_default_hidden('fl_encerra', '', 'N');
                
                foreach($collection as $item)
                {
                    echo form_start_box('default_box_pergunta_'.$item['cd_pos_venda_pergunta'], $item['ds_pergunta']);
                    
                        foreach($item['resposta'] as $item2)
                        {
                            $data_check['type'] = trim($item2['tp_resposta']);
                            $data_check['name'] = 'R_'.intval($item['cd_pos_venda_pergunta']).'[]';
                            $data_check['id']   = 'R_'.intval($item2['cd_pos_venda_resposta']).'[]';
                            
                            $data_textarea['name'] = 'C_'.intval($item2['cd_pos_venda_resposta']);
                            $data_textarea['id']   = 'C_'.intval($item2['cd_pos_venda_resposta']);
                            
                            echo
                            '<tr id="R_'.intval($item2['cd_pos_venda_resposta']).'_row">
                                <td class="coluna-padrao-form-objeto">
                                    '.form_checkbox($data_check, intval($item2['cd_pos_venda_resposta']), (trim($item2['fl_respondido']) == 'S' ? TRUE : FALSE), 'onclick="exibe_complemento(\'R_'.intval($item['cd_pos_venda_pergunta']).'\')"').'
                                    '.form_hidden('E_'.intval($item2['cd_pos_venda_resposta']), trim($item2['fl_complemento'])).'
                                    '.form_hidden('O_'.intval($item2['cd_pos_venda_resposta']), trim($item2['fl_complemento_obrigatorio'])).'
                                        
                                </td>
                                <td>
                                    <label for="R_'.intval($item2['cd_pos_venda_resposta']).'">
                                        '.trim($item2['ds_resposta']).'
                                    </label>
                                </td>  
                                <td style="width:40px;">
                                </td>
                                <td>
                                    '.form_textarea($data_textarea, $item2['complemento'], 'style="width:300px; height:40px; font-size:10pt; '.(((trim($item2['fl_complemento']) == "S") AND (trim($item2['fl_respondido']) == "S")) ? '' : 'display:none;').'"').'
                                    <span id="A_'.intval($item2['cd_pos_venda_resposta']).'"  style="color:red; font-weight:bold; '.(((trim($item2['fl_complemento'] == "S")) and (trim($item2['fl_respondido'] == "S")) and (trim($item2['fl_complemento_obrigatorio']) == "S")) ? '' : 'display:none;').'">* Obrigatório o preenchimento</span>
                                </td> 
                            </tr>';
                        }
                                        
                    echo form_end_box('default_box');
                }
                echo form_command_bar_detail_start();   
                    echo button_save('Salvar');
                    echo button_save('Encerrar', 'encerrar(form)', 'botao_vermelho');
                echo form_command_bar_detail_end();
            echo form_close();
        }
        echo br(2);
    echo aba_end();
    	
    $this->load->view('footer');
?>