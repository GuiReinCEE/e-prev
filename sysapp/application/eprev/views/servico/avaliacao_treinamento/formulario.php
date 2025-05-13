<?php
    set_title('Avaliação Treinamento');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>
    var campo = [];

    function ir_lista()
    {
        location.href = "<?= site_url('servico/avaliacao_treinamento') ?>";
    }

    function campo_observacao(cd, t)
    {
        if(jQuery.inArray(parseInt(t.val()), campo) > -1)
        {
            if(t.attr('checked'))
            {
                $("#estrutura_obs_"+cd+"_row").show();
            }
            else
            {
                $("#estrutura_obs_"+cd).val("");
                $("#estrutura_obs_"+cd+"_row").hide();
            }
        }
    }

    function finalizar_avaliacao(form)
    {
        var fl_salvar = true;

        <? foreach ($formulario['estrutura'] as $key => $item): ?>
            <? if((trim($item['tp']) == 'D') AND (trim($item['obr']) == 'S')): ?>

                if((fl_salvar) && ($("#estrutura_<?= $item['cd'] ?>").val()  == ''))
                {
                    alert('Preencha todos os campos com (*)');
                    fl_salvar = false;
                }

            <? elseif(trim($item['tp']) == 'O'): ?>
                <? if((count($item['sub']) == 0) AND (trim($item['obr']) == 'S')): ?>

                    if((fl_salvar) && ($("#estrutura_<?= $item['cd'] ?>").val()  == ''))
                    {
                        alert('Preencha todos os campos com (*)');
                        fl_salvar = false;
                    }

                <? elseif(count($item['sub']) > 0): ?>
                    <? foreach ($item['sub'] as $key2 => $item2): ?>
                        <? if(trim($item2['obr']) == 'S'): ?>
                            if((fl_salvar) && ($("#estrutura_<?= $item2['cd'] ?>").val()  == ''))
                            {
                                alert('Preencha todos os campos com (*)');
                                fl_salvar = false;
                            }
                        <? endif; ?>
                    <? endforeach; ?>
                <? endif; ?>
            <? elseif((trim($item['tp']) == 'S') AND (trim($item['obr']) == 'S')): ?>
                fl_marcado = false;

                $("input[type='checkbox'][id='estrutura_<?= $item['cd'] ?>']").each( 
                    function() 
                    { 
                        if(this.checked) 
                        { 
                            if(jQuery.inArray(parseInt($(this).val()), campo) > -1)
                            {
                                if((fl_salvar) && ($("#estrutura_obs_<?= $item['cd'] ?>").val()  == ''))
                                {
                                    alert('Preencha todos os campos com (*)');
                                    fl_salvar = false;
                                }
                            }

                            fl_marcado = true;
                        } 
                    }
                );              
                        
                if(!fl_marcado && fl_salvar)
                {
                    alert("Informe uma das opções.");
                    fl_salvar = false;
                }
            <? endif; ?>
        <? endforeach; ?>

        if(fl_salvar)
        {
             var confirmacao = "Deseja Finalizar a Avaliação?\n\n"+
                               "Clique [Ok] para Sim\n\n"+
                               "Clique [Cancelar] para Não\n\n"; 

            if(confirm(confirmacao))
            {
                form.method = "post";
                form.action = "<?= site_url('servico/avaliacao_treinamento/finalizar') ?>";
                form.target = "_self";
                form.submit();
            }
        }
    }

    $(function(){
        <? if(count($formulario['campo_adicional']) > 0): ?>
            <? foreach ($formulario['campo_adicional'] as $key => $item): ?>
                campo.push(parseInt(<?= $item ?>));

                <? if((isset($respostas['estrutura_'.$key])) AND (is_array($respostas['estrutura_'.$key])) AND (in_array($item, $respostas['estrutura_'.$key]))): ?>
                    $("#estrutura_obs_<?= $key ?>_row").show();
                <? else: ?>
                    $("#estrutura_obs_<?= $key ?>_row").hide();
                <? endif; ?>
                
            <? endforeach; ?>
        <? endif; ?>
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_formulario', 'Formulário', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('servico/avaliacao_treinamento/salvar');
            echo form_start_box('default_treinamento_box', 'Treinamento'); 
                echo form_default_hidden('cd_treinamento_colaborador_resposta', '', $treinamento['cd_treinamento_colaborador_resposta']);
				echo form_default_row('formulario', 'Formulário :', $treinamento['ds_treinamento_colaborador_formulario']);
				echo form_default_row('colaborador', 'Colaborador :', $treinamento['colaborador']);
				echo form_default_row('nome', 'Treinamento :', $treinamento['nome']);
				echo form_default_row('promotor', 'Promotor :', $treinamento['promotor']);
				echo form_default_row('dt_inicio', 'Período :', $treinamento['dt_inicio'].(trim($treinamento['dt_final']) != '' ? ' - '.$treinamento['dt_final'] : ''));
				echo form_default_row('dt_inclusao', 'Dt. Envio Avaliação :', $treinamento['dt_inclusao']);
                echo form_default_row('dt_alteracao', 'Dt. Última Alteração :', $treinamento['dt_alteracao']);
            echo form_end_box('default_treinamento_box');       

            foreach ($formulario['estrutura'] as $key => $item) 
            {
                if(trim($item['tp']) == 'D')
                {
                    echo form_start_box('default_'.$item['cd'].'_box', trim(utf8_decode($item['ds'])).(trim($item['obr']) == 'S' ? ' (*)' : '')); 
                        echo form_default_textarea('estrutura_'.$item['cd'], '', (isset($respostas['estrutura_'.$item['cd']]) ? trim(utf8_decode($respostas['estrutura_'.$item['cd']])) : ''));
                    echo form_end_box('default_'.$item['cd'].'_box');           
                }
                else if(trim($item['tp']) == 'O')
                {
                    if(count($item['sub']) == 0)
                    {
                        $opcoes = array();

                        foreach ($item['conf'] as $key2 => $item2) 
                        {
                            $opcoes[] = array(
                                'value' => $item2['cd'],
                                'text'  => utf8_decode($item2['ds'])
                            );
                        }

                        echo form_start_box('default_'.$item['cd'].'_box', trim(utf8_decode($item['ds'])).(trim($item['obr']) == 'S' ? ' (*)' : '')); 
                            echo form_default_dropdown('estrutura_'.$item['cd'], '', $opcoes, (isset($respostas['estrutura_'.$item['cd']]) ? $respostas['estrutura_'.$item['cd']] : ''));
                        echo form_end_box('default_'.$item['cd'].'_box');  
                    }
                    else
                    {
                        echo form_start_box('default_box', trim(utf8_decode($item['ds']))); 
                            foreach ($item['sub'] as $key2 => $item2) 
                            {
                                $opcoes = array();

                                foreach ($item2['conf'] as $key3 => $item3) 
                                {
                                    $opcoes[] = array(
                                        'value' => $item3['cd'],
                                        'text'  => utf8_decode($item3['ds'])
                                    );
                                }

                                echo form_default_dropdown('estrutura_'.$item2['cd'], trim(utf8_decode($item2['ds'])).(trim($item2['obr']) == 'S' ? ' (*)' : ''), $opcoes, (isset($respostas['estrutura_'.$item2['cd']]) ? $respostas['estrutura_'.$item2['cd']] : ''));
                            }
                        echo form_end_box('default_box');  
                    }
                }
                else if(trim($item['tp']) == 'S')
                {
                    $opcoes = array();

                    $fl_campo_adicional = false;

                    foreach ($item['conf'] as $key2 => $item2) 
                    {
                        $opcoes[] = array(
                            'value' => $item2['cd'],
                            'text'  => utf8_decode($item2['ds'])
                        );

                        if(trim($item2['obs']) == 'S')
                        {
                            $fl_campo_adicional = true;
                        }
                    }

                    echo form_start_box('default_'.$item['cd'].'_box', trim(utf8_decode($item['ds'])).(trim($item['obr']) == 'S' ? ' (*)' : '')); 
                        echo form_default_checkbox_group('estrutura_'.$item['cd'], '', $opcoes, (isset($respostas['estrutura_'.$item['cd']]) ? $respostas['estrutura_'.$item['cd']] : array()), 120, 500, 'onclick="campo_observacao('.$item['cd'].', $(this));"');
                        
                        if($fl_campo_adicional)
                        {
                            echo form_default_textarea('estrutura_obs_'.$item['cd'], 'Observações: (*)', (isset($respostas['estrutura_obs_'.$item['cd']]) ? trim(utf8_decode($respostas['estrutura_obs_'.$item['cd']])) : ''));
                        }

                    echo form_end_box('default_'.$item['cd'].'_box');           
                }
            }

            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
                echo button_save('Salvar e Finalizar Avaliação', 'finalizar_avaliacao(form)', 'botao_verde');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(3);
    echo aba_end();

    $this->load->view('footer_interna');
?>