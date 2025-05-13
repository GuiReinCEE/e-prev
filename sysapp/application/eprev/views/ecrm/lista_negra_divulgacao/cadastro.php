<?php
set_title('Divulgação - Lista Negra');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_lista_negra_divulgacao')) ?>    
   
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/lista_negra_divulgacao') ?>";
    }    

    function ir_emails()
    {
        location.href = "<?= site_url('ecrm/lista_negra_divulgacao/email/'.intval($row['cd_lista_negra_divulgacao'])) ?>";
    } 
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    if(intval($row['cd_lista_negra_divulgacao']) > 0)
    { 
        $abas[] = array('aba_email', 'Lista de E-mail', FALSE, 'ir_emails();');
    }

    echo aba_start($abas);
        echo form_open('ecrm/lista_negra_divulgacao/salvar');
            echo form_start_box('default_box', 'Cadastro');			
    			echo form_default_hidden('cd_lista_negra_divulgacao', '', $row['cd_lista_negra_divulgacao']);
             echo form_default_text('ds_lista_negra_divulgacao', 'Descrição: (*)', $row['ds_lista_negra_divulgacao'], 'style="width:350px;"'); 
             echo form_end_box('default_box');                
                echo form_command_bar_detail_start();
                    echo button_save('Salvar');
                echo form_command_bar_detail_end();
            echo form_end_box('default_box');
            
        echo form_close();
    echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>