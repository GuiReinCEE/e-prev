<?php
	set_title('Rodízio - Atendimento - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('dt_atendimento_rodizio', 'tp_turno', 'tp_posicao')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/atendimento_rodizio/index') ?>";
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    $turno = array(
        array('value' => 'M', 'text' => 'Manhã'), 
        array('value' => 'T', 'text' => 'Tarde')
    );             

    $posicao = array(
        array('value'=> 'T', 'text' => 'Telefone'),
        array('value'=> 'P', 'text' => 'Atendimento Pessoal')        
    );

    echo aba_start($abas);
    	echo form_open('ecrm/atendimento_rodizio/salvar');
    		echo form_start_box('default_box', 'Cadastro'); 
	    		echo form_default_hidden('cd_atendimento_rodizio', '',$row['cd_atendimento_rodizio']);
                echo form_default_date('dt_atendimento_rodizio', 'Dt. Atendimento', $row['dt_atendimento_rodizio']);
                echo form_default_dropdown('tp_turno', 'Turno: (*)', $turno, $row['tp_turno']);                   
	    	echo form_end_box('default_box');

            echo form_start_box('default_box', 'Atendente');
                
                foreach($atendente as $key => $item)
                {
                    // echo form_default_row($key['text'], 'Atendente:', $atendente[$key]['text']);
                    echo form_default_dropdown('atendente['.$item['cd_usuario'].']', $atendente[$key]['ds_nome'], $posicao, $item['tp_posicao']);
                }     

            echo form_end_box('default_box');

	    	echo form_command_bar_detail_start();
             echo button_save('Salvar'); 
            echo form_command_bar_detail_end();		
    	echo form_close();
    echo aba_end();

    $this->load->view('footer_interna');
?>