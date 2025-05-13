<?php
	set_title('Canal de sugestões');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_assunto', 'ds_descricao')) ?>
</script>
<?php
	$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_open('cadastro/canal_sugestao/salvar');

            if(trim($row['fl_solicitacao']) == 'S')
            {
                echo '<center><h1 style="color:red; font-size:120%;">Contato Enviado.</h1></center>';
            }

            echo form_start_box('default_box', 'Cadastro');
            
                echo form_default_text('ds_assunto', 'Assunto: (*)', $row['ds_assunto'], 'style="width:350px;"');
                echo form_default_textarea('ds_descricao', 'Descrição: (*)', $row['ds_descricao'], 'style="height:100px;"');
                
            echo form_end_box('default_box');   
            echo form_command_bar_detail_start();
                echo button_save('Encaminhar');            
            echo form_command_bar_detail_end();
     
		echo form_close();
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>