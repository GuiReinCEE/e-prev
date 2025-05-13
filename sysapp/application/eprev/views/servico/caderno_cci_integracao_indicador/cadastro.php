<?php
set_title('Caderno CCI Integração Indicador - Cadastro');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_indicador', 'ds_caderno_cci_integracao_indicador')) ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/caderno_cci_integracao_indicador') ?>";
    }
	
	function ir_campo_integracao()
	{
        location.href = "<?= site_url('servico/caderno_cci_integracao_indicador/campo_integracao/'.intval($row['cd_caderno_cci_integracao_indicador'])) ?>";
	}

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	if(intval($row['cd_caderno_cci_integracao_indicador']) > 0)
	{
		$abas[] = array('aba_campo_integracao', 'Campo Integração', FALSE, 'ir_campo_integracao();');
	}
	
    echo aba_start( $abas );
        echo form_open('servico/caderno_cci_integracao_indicador/salvar');
            echo form_start_box('default_box', 'Cadastro'); 
				echo form_default_hidden('cd_caderno_cci_integracao_indicador', '', $row['cd_caderno_cci_integracao_indicador']);
				echo form_default_text('ds_indicador', 'Indicador: (*)', $row['ds_indicador'], 'style="width:300px;"');
				echo form_default_text('ds_caderno_cci_integracao_indicador', 'Referência: (*)', $row['ds_caderno_cci_integracao_indicador'], 'style="width:300px;"');
			echo form_end_box('default_box'); 
			echo form_command_bar_detail_start();   
                echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>