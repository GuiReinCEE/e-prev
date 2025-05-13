<?php
set_title('Enviar Senha');
$this->load->view('header');
?>
<script>
    function carregar_dados_participante(data)
    {        
        $.post( '<?php echo site_url("ecrm/envia_senha/tipo_senha"); ?>/',
        {
            cd_empresa            : data.cd_empresa,
            cd_registro_empregado : data.cd_registro_empregado,
            seq_dependencia       : data.seq_dependencia
        },
        function(retorno)
        {
            switch (retorno)
            {
                case '1':
                    $('#fl_tipo').val('1 - CONSULTA');
                    break;
                case '2':
                    $('#fl_tipo').val('2 - COMPLETA');
                    break;
                 default :
                     $('#fl_tipo').val('0 - NÃO POSSUI');
                     break;
            }  
            
            $('#nome').val(data.nome);
            $('#email').val(data.email);
            $('#email_profissional').val(data.email_profissional);   
        }); 
    }
    
    function enviar_senha()
    {
        var confirmacao = 'ATENÇÃO esta ação é IRREVERSÍVEL.\n\n' +
                          'Confira  o participante informado antes de enviar o email.\n\n' +
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
							  
        
        if(confirm(confirmacao))
        {
            link = $('#cd_empresa').val()+'/'+$('#cd_registro_empregado').val()+'/'+$('#seq_dependencia').val();

            $.post( '<?php echo site_url('ecrm/envia_senha/enviar') ?>/'+link,'',
            function(data)
            {
                if(data == '1')
                {
                    alert('E-mail enviado com sucesso.');
                }
                else
                {
                    alert('E-mail não foi enviado. Verique o participante informado.');
                }
            });
        }
    }
    
    function ir_relatorio()
    {
        location.href='<?php echo site_url('ecrm/envia_senha/relatorio') ?>';
    }
    
</script>
<?php

$abas[] = array('aba_lista', 'Enviar senha', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Relatório', FALSE, 'ir_relatorio();');
echo aba_start($abas);
    echo form_start_box( "default_box", "Enviar Senha" );
        $c['emp']['id']='cd_empresa';
        $c['re']['id']='cd_registro_empregado';
        $c['seq']['id']='seq_dependencia';
        $c['emp']['value'] = $cd_empresa;
        $c['re']['value']  = $cd_registro_empregado;
        $c['seq']['value'] = $seq_dependencia;
        $c['caption']='Participante:*';
        $c['callback']='carregar_dados_participante';
        echo form_default_participante_trigger($c);
        echo form_default_text("nome", "Nome:", '', "style='width:100%;border: 0px; font-weight:bold;' readonly");
        echo form_default_text("fl_tipo", "Tipo de senha:", '', "style='width:100%; border: 0px; color:red; font-weight:bold;' readonly");
        echo form_default_text("email", "Email:", '', "style='width:100%;border: 0px;' readonly");
        echo form_default_text("email_profissional", "Email Prossifional:", '', "style='width:100%;border: 0px;' readonly");
    echo form_end_box("default_box");
    echo form_command_bar_detail_start();
            echo button_save("Enviar", 'enviar_senha()');
    echo form_command_bar_detail_end();
echo aba_end();
?>
<script>
    $(document).ready(function()
	{
		if($("#cd_registro_empregado").val() > 0)
		{
			consultar_participante__cd_empresa();
		}
    });	
</script>
<?php
$this->load->view('footer');
?>