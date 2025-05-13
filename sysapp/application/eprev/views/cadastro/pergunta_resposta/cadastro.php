<?php
    set_title('Sistema de Perguntas e Respostas - Cadastro');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_pergunta')); ?>

    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/pergunta_resposta') ?>";
    }

	function get_usuarios()
	{		
		$.post("<?= site_url('cadastro/pergunta_resposta/get_usuarios') ?>",
		{
			cd_gerencia_responsavel : $("#cd_gerencia_responsavel").val()
		},
		function(data)
		{ 
			var select = $('#cd_usuario_responsavel'); 
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			   
			$('option', select).remove();
			   
			options[options.length] = new Option('Selecione', '');
			   
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
		}, 'json');
    }
    
    function valida_responsavel()
    {
        if($("#cd_gerencia_responsavel").val() != '' && $("#cd_usuario_responsavel").val() != '')
        {
            var confirmacao = "Salvar?";

            if(confirm(confirmacao))
            {
                $("form").submit();
            }
        }
        else if($("#cd_gerencia_responsavel").val() == '')
        {
            var confirmacao = "Indique a Gerência Responsável.\n\n"+
                              "Clique [Ok] para Sim\n\n"+
		                      "Clique [Cancelar] para Não\n\n";


            if(confirm(confirmacao))
            {
                $( "#cd_gerencia_responsavel" ).focus();
            }
        }
        else if($("#cd_usuario_responsavel").val() == '')
        {
            var confirmacao = "Indique o Usuário Responsável.\n\n"+
                              "Clique [Ok] para Sim\n\n"+
		                      "Clique [Cancelar] para Não\n\n";

            if(confirm(confirmacao))
            {
                $( "#cd_usuario_responsavel" ).focus();
            }
        }
    }

    function valida_resposta()
    {
        if($("#ds_resposta").val() != '')
        {
            var confirmacao = "Salvar?";

            if(confirm(confirmacao))
            {
                $("form").submit();
            }
            
        }
        else
        {
            confirmacao = 'Para salvar preencha  o campo Resposta.\n\n'+
		                  'Clique [Ok] para Sim\n\n'+
		                  'Clique [Cancelar] para Não\n\n';

            if(confirm(confirmacao))
            {
                $( "#ds_resposta" ).focus();
            }
        }
    }
</script>
<?php
    $abas[] = array( 'aba_lista', 'Lista', FALSE, 'ir_lista();' );
    $abas[] = array( 'aba_lista', 'Cadastro', TRUE, 'location.reload();' );

    echo aba_start($abas);
        echo form_open('cadastro/pergunta_resposta/salvar');
            echo form_start_box('default_cadastro_box', 'Cadastro');
                echo form_default_hidden('cd_pergunta_resposta', '', $row['cd_pergunta_resposta']);
                echo form_default_textarea('ds_pergunta', 'Pergunta: (*)', $row);
            echo form_end_box('default_cadastro_box');
            echo form_command_bar_detail_start();
                if(intval($row['cd_pergunta_resposta']) == 0)
                {
                    echo button_save('Salvar');	
                }
            echo form_command_bar_detail_end();
        echo form_close();
        if(trim($row['fl_usuario_rh']) == 'S' AND trim($row['dt_encaminha_responsavel']) == '')
        {
            echo form_open('cadastro/pergunta_resposta/salvar_responsavel');
                echo form_start_box('default_responsavel_box', 'Responsável');
                    echo form_default_hidden('cd_pergunta_resposta', '', $row['cd_pergunta_resposta']);
                    echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência: (*)', $row['cd_gerencia_responsavel'], 'onchange="get_usuarios()"');
                    echo form_default_dropdown('cd_usuario_responsavel', 'Responsável: (*)', $usuarios, $row['cd_usuario_responsavel']); 
                echo form_end_box('default_responsavel_box');
                echo form_command_bar_detail_start();
                    echo button_save('Salvar', 'valida_responsavel()');	
                echo form_command_bar_detail_end();
            echo form_close();
        }
        else if(trim($row['dt_encaminha_responsavel']) != '')
        {
            echo form_start_box('default_responsavel_box', 'Responsável');
                echo form_default_row('', 'Gerência:', $row['cd_gerencia_responsavel']);
                echo form_default_row('', 'Responsável:', $row['ds_usuario_responsavel']); 
                echo form_default_row('', 'Dt. Encaminhada Responsável:', '<span class="label label-info">'.$row['dt_encaminha_responsavel'].'</span>');
            echo form_end_box('default_responsavel_box');
        }

        if(trim($row['dt_encaminha_responsavel']) != '' AND intval($row['cd_usuario_responsavel']) == intval($cd_usuario) OR trim($row['dt_resposta']) != '')
        {
            echo form_open('cadastro/pergunta_resposta/salvar_resposta');
                echo form_start_box('default_responder_box', 'Responder');
                    echo form_default_hidden('cd_pergunta_resposta', '', $row['cd_pergunta_resposta']);
                    echo form_default_textarea('ds_resposta', 'Resposta: (*)' , $row['ds_resposta']);
                echo form_end_box('default_responder_box');
                echo form_command_bar_detail_start();
                    if(trim($row['dt_resposta']) == '')
                    {
                        echo button_save('Salvar', 'valida_resposta()');	
                    }
                echo form_command_bar_detail_end();
            echo form_close();
        }
    echo aba_end();

    $this->load->view('footer');
?>