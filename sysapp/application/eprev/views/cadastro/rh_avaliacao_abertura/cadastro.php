<?php
	set_title('Sistema de Avaliação - Abertura');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('nr_ano_avaliacao', 'dt_inicio', 'dt_encerramento', 'ds_instrucao_preenchimento')); ?>

    function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

    function ir_avaliacao()
    {
        location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$row['cd_avaliacao']) ?>";
    }

    function ir_relatorio()
    {
        location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/relatorio/'.$row['cd_avaliacao']) ?>";
    }

	function ir_pdi()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/relatorio_pdi/'.$row['cd_avaliacao']) ?>";
	}

    function encaminhar_email()
    {
        var text = "Deseja encaminhar um e-mail para os avaliados?\n\n"+
                   "[OK] para Sim\n\n"+
                   "[Cancelar] para Não\n\n";

        if(confirm(text))
        {
            location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/encaminhar_email/'.$row['cd_avaliacao']) ?>";
        }
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_avaliacao']) > 0)
    {
        $abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
        $abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');
        $abas[] = array('aba_relatorio_pdi', 'PDI', FALSE, 'ir_pdi();');
    }

    echo aba_start($abas); 
        echo form_open('cadastro/rh_avaliacao_abertura/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_avaliacao', '', $row);	
                if(intval($row['cd_avaliacao']) == 0)
                {
                    echo form_default_integer('nr_ano_avaliacao', 'Ano: (*)', $row);
                }
                else
                {
                    echo form_default_row('', 'Ano:', '<span class="label label-inverse">'.$row['nr_ano_avaliacao'].'</span>');
                }
                
                echo form_default_date('dt_inicio', 'Dt. Ínicio: (*)', $row);
                echo form_default_date('dt_encerramento', 'Dt. Encerramento: (*)', $row);
                echo form_default_textarea('ds_instrucao_preenchimento', 'Instruções de Preenchimento: (*)', $row);
                if(trim($row['dt_envio_email']) != '')
                {
                    echo form_default_row('', 'Dt. Envio:', $row['dt_envio_email']);
                    echo form_default_row('', 'Usuário Envio:', $row['ds_usuario_envio_email']);
                }
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar'); 

                if(intval($row['cd_avaliacao']) > 0)
                {
                    echo button_save('Encaminhar E-mail', 'encaminhar_email();', 'botao_verde'); 
                }  

			echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>