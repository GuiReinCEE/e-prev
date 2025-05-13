<?php
set_title("Pós-Venda - Iniciar");
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia'), 'form_pos_venda(form);');
?>
    function form_pos_venda(form)
    {
        location.href='<?php echo site_url('ecrm/posvenda/posvenda_participante'); ?>/'+$('#cd_empresa').val()+'/'+$('#cd_registro_empregado').val()+'/'+$('#seq_dependencia').val();

        return false;
    }

    function ir_emails()
	{
		location.href='<?php echo site_url('ecrm/posvenda/envia_email'); ?>';
	}
    
    function ir_relatorio()
	{
		location.href='<?php echo site_url('ecrm/posvenda/relatorio'); ?>';
	}
    
    function ir_lista()
	{
		location.href='<?php echo site_url('ecrm/posvenda'); ?>';
	}
</script>
<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_envia_email', 'Emails', FALSE, 'ir_emails();');
$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');
$abas[] = array('aba_iniciar', 'Iniciar', TRUE, "location.reload();");

$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');

echo aba_start( $abas );
    echo form_open('');
        echo form_start_box( "default_box", "Cadastro" );
            echo form_default_participante($conf, "Participante :", Array('cd_empresa' => '', 'cd_registro_empregado' => '', 'seq_dependencia' => 0), false);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();   
            echo button_save("Iniciar Pós-Venda");
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);
echo aba_end();
	
$this->load->view('footer');
?>