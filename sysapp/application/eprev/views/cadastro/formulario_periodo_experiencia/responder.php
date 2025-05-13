<?php
	set_title('Formulário');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_formulario(form, false)') ?>

    function valida_formulario(form, valida)
    {
        var radio = [];

        var check = true;

        <?php 
            foreach ($formulario as $key => $item)
            {
        ?>
                radio.push({
                    "cd_grupo"    : <?= $key ?>,
                    "grupo"       : "cd_grupo_<?= $key ?>", 
                    "check"       : false,
                    "cd_pergunta" : null 
                });
        <?php 
            }
        ?>
        
        $.each(radio, function(i, item){
            $("input[name='"+item.grupo+"']").each(function(){

                if($(this).attr("checked") == true)
                {
                    item.check = true;
                    item.cd_pergunta = $(this).val()
                    return true;
                }
            });
        });

        var fl_submit = true;

        if(valida)
        {
            $("#fl_encerrar").val("S");

            confirmacao = 'Deseja salvar e encaminhar o formulário para o RH?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

            $.each(radio, function(i, item){
                if(!item.check)
                {
                    fl_submit = false;
                    return false;
                }
            });
        }
        else
        {
            var confirmacao = 'Deseja salvar o formulário? \n\n'+
                '(Para encerrar clique em Salvar e Encaminhar) \n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        }

        if(fl_submit)
        {
            if(confirm(confirmacao))
            {
                form.submit();
            } 
        }
        else
        {
            $("#fl_encerrar").val("N");
            alert("Responda todos os campos antes de encaminhar.");
        }
    }

    function encaminhar(form)
    {

        valida_formulario(form, true);
    }

    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/formulario_periodo_experiencia/minhas') ?>";
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Responder', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_start_box('default_box', $row['ds_formulario_periodo_experiencia']); 
            echo form_default_row('', 'Colaborador:', $row['ds_usuario_avaliado']);
            echo form_default_row('', 'Cargo:', $row['ds_cargo']);
            echo form_default_row('', 'Admissão:', $row['dt_admissao']);
            echo form_default_row('', 'Área:', $row['divisao']);
            echo form_default_row('', 'Devolução até:', $row['dt_limite']);
        echo form_end_box('default_box');
        echo br();
        echo nl2br($row['ds_descricao']);
        echo br();
    	echo form_open('cadastro/formulario_periodo_experiencia/salvar_resposta');
            echo form_default_hidden('cd_formulario_periodo_experiencia_solic', '', $row['cd_formulario_periodo_experiencia_solic']);
            echo form_default_hidden('fl_encerrar', '', 'N');

            $pergunta = array();
            
            foreach ($formulario as $key => $item)
            {
                echo form_start_box('default_box_'.$key, trim(utf8_decode($item['ds_grupo']))); 
                    
                    foreach($item['pergunta'] as $key2 => $item2)
                    {
                        $radio = array(
                            'name' => 'cd_grupo_'.$key,
                            'id'   => 'cd_grupo_'.$key
                        );

                        echo form_default_row('', form_radio($radio, $key2, (intval($resposta[$key]) == $key2 ? TRUE : FALSE)), trim(utf8_decode($item2)));
                    }
                echo form_end_box('default_box_'.$key);
            }

	    	echo form_command_bar_detail_start();
                echo button_save('Salvar');
                echo button_save('Salvar e Encaminhar', 'encaminhar(form)', 'botao_verde');
            echo form_command_bar_detail_end();
    	echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>