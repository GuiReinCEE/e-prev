<?php
    set_title('Formulários - Inscrição');
    $this->load->view('header');
?>
<script>    
    function imprimir()
    {
        if($("#cd_empresa").val() != "" &&  $("#cd_empresa").val() != $("#cd_plano_empresa").val())
        {
             alert("Empresa do participante é diferente da empresa do plano.");
        }
        else if($("#cd_empresa").val() != "" && $("#seq_dependencia").val() > 0)
        {
            alert("Dependencia não pode ser igual a zero(0).");
        }
        else
        {
            if($("#cd_plano").val() != "")
            {
               var link = $("#cd_plano").val()+"/"+$("#cd_plano_empresa").val()+"/"+$("#cd_registro_empregado").val()+"/"+$("#seq_dependencia").val();

                location.href = "<?= site_url('ecrm/formulario_cadastro/gera') ?>/"+link;
            }
            else
            {
                alert("Plano deve ser informado.");
            }
        }
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Inscrição', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_start_box('default_box', 'Inscrição');
            echo filter_plano_empresa_ajax('cd_plano', '', '', 'Plano:', 'Empresa:');
            echo form_default_participante(array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante'),'Participante (Emp/RE/Seq):', false, false, true);
        echo form_end_box('default_box');
        echo form_command_bar_detail_start();
            echo button_save('Imprimir', 'imprimir()');
        echo form_command_bar_detail_end();
        echo br(3);
    echo aba_end();

$this->load->view('footer');
?>