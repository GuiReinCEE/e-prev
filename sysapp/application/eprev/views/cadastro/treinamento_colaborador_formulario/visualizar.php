<?php
    set_title('Formulário de Treinamento');
    $this->load->view('header');
?>
<script>
    var campo = [];

    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_colaborador_formulario') ?>";
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

    $(function(){
        <? if(count($formulario['campo_adicional']) > 0): ?>
            <? foreach ($formulario['campo_adicional'] as $key => $item): ?>
                campo.push(parseInt(<?= $item ?>));
                $("#estrutura_obs_<?= $key ?>_row").hide();
            <? endforeach; ?>
        <? endif; ?>
    });
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_formulario', 'Formulário', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('');
            
            foreach ($formulario['estrutura'] as $key => $item) 
            {
                if(trim($item['tp']) == 'D')
                {
                    echo form_start_box('default_'.$item['cd'].'_box', trim(utf8_decode($item['ds'])).(trim($item['obr']) == 'S' ? ' (*)' : '')); 
                        echo form_default_textarea('estrutura['.$item['cd'].']', '');
                    echo form_end_box('default_box');           
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
                            echo form_default_dropdown('estrutura_'.$item['cd'], '',$opcoes);
                        echo form_end_box('default_box');  
                    }
                    else
                    {
                        echo form_start_box('default_box', trim($item['ds'])); 
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

                                echo form_default_dropdown('estrutura_'.$item2['cd'], trim(utf8_decode($item2['ds'])).(trim($item2['obr']) == 'S' ? ' (*)' : ''), $opcoes);
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
                        echo form_default_checkbox_group('estrutura_'.$item['cd'], '', $opcoes, array(), 120, 500, 'onclick="campo_observacao('.$item['cd'].', $(this));"');
                        
                        if($fl_campo_adicional)
                        {
                            echo form_default_textarea('estrutura_obs_'.$item['cd'], 'Observações: (*)');
                        }

                    echo form_end_box('default_box');           
                }
            }

            echo form_command_bar_detail_start();   
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>