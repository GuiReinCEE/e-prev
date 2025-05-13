<?php
    set_title('Pós-Venda - Relatório');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('dt_ini', 'dt_fim'), 'gerar(form)');  ?>

    function ir_relatorio_email()
    {
        location.href = "<?= site_url('ecrm/posvenda/relatorio_email') ?>";
    }
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/posvenda') ?>";
    }
    
    function gerar(form)
    {
        if(confirm('Gerar?'))
        {
            form.submit();
        }
    }
</script>
<?php

    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_envia_email', 'Emails', FALSE, 'ir_relatorio_email();');
    $abas[] = array('aba_relatorio', 'Relatório', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_open('ecrm/posvenda/gera_relatorio', array('target' => '_blank'));
            echo form_start_box('default_box', 'Relatório');
                echo form_default_date_interval('dt_ini', 'dt_fim', 'Data: (*)');
                echo form_default_dropdown('cd_plano', 'Plano:', $plano);
                echo form_default_dropdown('cd_empresa', 'Empresa:', $patrocinadora);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();   
                echo button_save('Gerar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();
	
    $this->load->view('footer');
?>