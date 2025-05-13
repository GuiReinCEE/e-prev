<?php
    set_title('Súmulas Interventor - Cadastro');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(Array('nr_sumula_interventor', 'dt_sumula_interventor', 'dt_divulgacao'), 'validacao(form);') ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/sumula_interventor_inicial') ?>";
    }
    
    function validacao(form)
    {
        $.post("<?= site_url('gestao/sumula_interventor_inicial/valida_numero_sumula') ?>",
        {
            nr_sumula_interventor : $("#nr_sumula_interventor").val(),
            cd_sumula_interventor : $("#cd_sumula_interventor").val()
        },
        function(data)
        {             
            if(data["valida"] == 0)
            {
                if(($("#arquivo_pauta").val() == "") && ($("#arquivo_pauta_nome").val() == ""))
                {
                    alert("Favor anexar a Pauta.");
                    return false;
                }
                else if(($("#arquivo_sumula").val() == "") && ($("#arquivo_sumula_nome").val() == ""))
                {
                    alert("Favor anexar a Súmula.");
                    return false;
                }
                else
                {
                    if(confirm("Salvar?"))
                    {
                        $("form").submit();  
                    }
                }
            }
            else if(data["valida"] == 1) 
            {
                alert("Número de sumula já existe.");
                return false; 
            }
        },'json', true);
    }
    
	function publicar()
	{
        var confirmacao = 'Confirma a publicação?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            var cd_sumula_interventor  = $("#cd_sumula_interventor").val();
            var dt_publicacao_libera   = $("#dt_publicacao_libera").val();

			$(".div_aba_content").html("<center><BR><BR><BR><BR><b>AGUARDE...</b><BR><BR><?= loader_html() ?></center>");
			$.post("<?= site_url('gestao/sumula_interventor_inicial/publicar') ?>",
			{
				cd_sumula_interventor : cd_sumula_interventor,
				dt_publicacao_libera  : dt_publicacao_libera
			},
			function(data)
			{
				location.reload();
			});            
        }		
	}	

    function enviar_fundacao()
    {
        var confirmacao = 'Deseja enviar email para todos?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/sumula_interventor_inicial/enviar_fundacao/'.$row['cd_sumula_interventor']) ?>";
        }
    }
    
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('gestao/sumula_interventor_inicial/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_sumula_interventor', '', $row['cd_sumula_interventor']);
                echo form_default_hidden('nr_sumula_interventor_salvo', '', $row['nr_sumula_interventor']);
                echo form_default_integer('nr_sumula_interventor', 'Número: (*)', $row);
                echo form_default_date('dt_sumula_interventor', 'Data: (*)', $row);
                echo form_default_date('dt_divulgacao', 'Dt. Divugalção: (*)', $row);
                echo form_default_upload_iframe('arquivo_pauta', 'sumula_interventor_inicial', 'Arquivo Pauta: (*)', array($row['arquivo_pauta'], $row['arquivo_pauta_nome']), 'sumula_interventor_inicial');
                echo form_default_upload_iframe('arquivo_sumula', 'sumula_interventor_inicial', 'Arquivo Súmula: (*)', array($row['arquivo_sumula'], $row['arquivo_sumula_nome']), 'sumula_interventor_inicial');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();     
                if(intval($row['cd_sumula_interventor']) == 0)
                {
                    echo button_save('Salvar');
                } 
                else
                {
                    echo button_save('Enviar Emails para Todos', 'enviar_fundacao()', 'botao_verde');
                }
            echo form_command_bar_detail_end();
        echo form_close();

        if(intval($row['cd_sumula_interventor']) > 0)
        {
            echo form_start_box('default_box', 'Publicação' );
                echo form_default_date('dt_publicacao_libera', 'Dt. Autoatendimento: (*)', $row);
                if(trim($row['dt_publicacao_libera']) != '')
                {
                    echo form_default_row('', 'Dt. Publicação:', $row['dt_publicacao']);
                    echo form_default_row('', 'Usuário Publicação:', $row['ds_usuario_publicacao']);
                }
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();     
                echo button_save('Salvar', 'publicar()', 'botao_vermelho');
            echo form_command_bar_detail_end();
        } 

        echo br(2);	
    echo aba_end();

    $this->load->view('footer_interna');
?>