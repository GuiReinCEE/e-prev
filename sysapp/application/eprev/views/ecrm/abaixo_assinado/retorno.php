<?php
	set_title('Abaixo Assinado - Retorno');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_abaixo_assinado', 'ds_acompanhamento')); ?>

    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/cadastro/'.$cd_abaixo_assinado) ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/acompanhamento/'.$cd_abaixo_assinado) ?>";
    }

    function ir_anexo()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/anexo/'.$cd_abaixo_assinado) ?>";
    }
	
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
    $abas[] = array('aba_retorno', 'Retorno', TRUE, 'location.reload();');

    echo aba_start($abas);
        echo form_start_box('default_cadastro_box', 'Cadastro');
            echo form_default_row('', 'Ano/N°:', '<span class="label label-inverse">'.$row['nr_numero_ano'].'</span>');
            echo form_default_row('', 'Dt. Protocolo:', '<span class="label label-info">'.$row['dt_protocolo'].'</span>');
            echo form_default_row('', 'Dt. Limite Retorno:', '<span class="label label-important">'.$row['dt_limite_retorno'].'</span>');
            echo form_default_row('', 'RE:', $row['nr_re']);
            echo form_default_row('', 'Nome:', $row['ds_nome']);
        echo form_end_box('default_cadastro_box');
        echo form_open('ecrm/abaixo_assinado/salvar_retorno');
	        echo form_start_box('default_retorno_box', 'Retorno');
	        	echo form_default_hidden('cd_abaixo_assinado', '', $cd_abaixo_assinado);
	        	echo form_default_textarea('ds_retorno', 'Descrição: (*)', $row, 'style="height:80px;"');
	        	echo form_default_date('dt_retorno', 'Dt. Retorno: (*)', $row);
	        	echo form_default_dropdown('cd_abaixo_assinado_retorno_tipo', 'Forma de Retorno: (*)', $tipo, $row['cd_abaixo_assinado_retorno_tipo']);
	        echo form_end_box('default_retorno_box');
	        echo form_command_bar_detail_start();
		        if(trim($row['ds_acao']) != '' AND trim($row['dt_retorno']) == '')
		        {
		            echo button_save('Salvar');
		        }
		        else if(trim($row['ds_acao']) == '')
		        {
		        	echo '<span style="text-align:center; color:red; font-weight:bold;">Para registrar o retorno deve ser cadastrado a ação na aba Cadastro.</span>';
		        }
	        echo form_command_bar_detail_end();
        echo form_close();
    echo aba_end();
    echo br(2);
    $this->load->view('footer');
?>