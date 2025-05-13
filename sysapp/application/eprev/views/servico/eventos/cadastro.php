<?php
set_title('Eventos - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('fl_tipo', 'nome', 'assunto')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/eventos') ?>";
    }
	
    function ir_envia_email()
    {
        location.href = "<?= site_url('servico/eventos/envia_email/'.intval($row['cd_evento'])) ?>";
    }

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
	
	if(intval($row['cd_evento'] > 0))
	{
		$abas[] = array('aba_emails_enviado', 'E-mails Enviado', FALSE, 'ir_envia_email();');
	}
	
    echo aba_start( $abas );
        echo form_open('servico/eventos/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_evento', '', $row['cd_evento']);
				if(intval($row['cd_evento'] > 0))
				{	
					echo form_default_row('cd_evento', 'Cód. Evento:', '<span class="label label-inverse">'.$row['cd_evento'].'</span>');
				}
				echo form_default_dropdown('fl_tipo', 'Tipo: (*)', $tipo, $row['fl_tipo']);
				echo form_default_text('nome', 'Nome: (*)', $row, 'style="width:300px;"');
				echo form_default_text('assunto', 'Assunto: (*)', $row, 'style="width:300px;"');
				echo form_default_text('para', 'Para:', $row, 'style="width:300px;"');
				echo form_default_text('cc', 'CC:', $row, 'style="width:300px;"');
				echo form_default_text('cco', 'CCO:', $row, 'style="width:300px;"');
				echo form_default_editor_html('email', 'Email: (*)', $row, 'style="height: 400px;"');
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>