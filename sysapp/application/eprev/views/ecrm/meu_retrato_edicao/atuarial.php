<?php
	set_title('Meu Retrato Edição');
	$this->load->view('header');
?>
<script>
	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_participante()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante/'.$row['cd_edicao']) ?>";
    }

    function gerar_csv_instituidor_ativo()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_instituidor/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_aposentado()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_aposentado/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_ieabprev()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_ieabprev/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_municipios()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_municipios/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_familia_corporativo()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_familia_corporativo/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_ceeeprev()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_ceeeprev/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_ceranprev()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_ceranprev/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_crmprev()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_crmprev/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function gerar_csv_foz()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante_dados_foz/'.$row['cd_edicao'].'/'.$row['cd_plano'].'/'.$row['cd_empresa']) ?>";
    }

    function libera_atuarial()
    {
    	var confirmacao = "Deseja Liberar o Meu Reatrato para a GRC?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/meu_retrato_edicao/libera_atuarial/'.$row['cd_edicao']) ?>";
        }
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_participante', 'Meu Retrato Participante', FALSE, 'ir_participante();');

    echo aba_start($abas);

        echo form_start_box('default_box', 'Cadastro');	
        	echo form_default_row('cd_edicao', 'Edição:', '<span class="label label-inverse">'.$row['cd_edicao'].'</span>');
            echo form_default_row('sigla', 'Empresa:', $row['sigla']);   
            echo form_default_row('plano', 'Plano:', $row['plano']);   
        	echo form_default_row('nr_extrato', 'Nº Extrato:', $row['nr_extrato']);   	
    		echo form_default_row('dt_base_extrato', 'Dt. Base:', $row['dt_base_extrato']);
        	echo form_default_row('dt_inclusao', 'Dt. Inclusão:', '<span class="label">'.$row['dt_inclusao'].'</span>');
            echo form_default_row('usuario_inclusao', 'Usuário Inclusão:', '<span class="label">'.$row['usuario_inclusao'].'</span>');
            echo form_default_row('dt_alteracao', 'Dt. Alteração:', '<span class="label label-info">'.$row['dt_alteracao'].'</span>');
            echo form_default_row('usuario_alteracao', 'Usuário Alteração:', '<span class="label label-info">'.$row['usuario_alteracao'].'</span>');
            echo form_default_row('dt_liberacao_informatica', 'Dt. Informática:', '<span class="label label-warning">'.$row['dt_liberacao_informatica'].'</span>');
            echo form_default_row('usuario_informatica', 'Usuário Informática:', '<span class="label label-warning">'.$row['usuario_informatica'].'</span>');

            if(trim($row['dt_liberacao_atuarial']) != '')
            {
                echo form_default_row('dt_liberacao_atuarial', 'Dt. Atuarial/Benefício:',  '<span class="label label-important">'.$row['dt_liberacao_atuarial'].'</span>');
                echo form_default_row('usuario_atuarial', 'Usuário:',  '<span class="label label-important">'.$row['usuario_atuarial'].'</span>');
            }

            if(trim($row['dt_liberacao_comunicacao']) != '')
            {
                echo form_default_row('dt_liberacao_comunicacao', 'Dt. Comunicação:', '<span class="label label-success">'.$row['dt_liberacao_comunicacao'].'</span>');
                echo form_default_row('usuario_comunicacao', 'Usuário:', '<span class="label label-success">'.$row['usuario_comunicacao'].'</span>');
            }

        echo form_end_box('default_box');
        echo form_command_bar_detail_start();

        	if((trim($row['dt_liberacao_informatica']) != '') AND (trim($row['dt_liberacao_atuarial']) == ''))
        	{
                if(gerencia_in(array('GP')))
                {  
                    echo button_save('Liberar', 'libera_atuarial();', 'botao_verde');

                    if(trim($row['tp_participante']) == 'ATIV' AND in_array($row['cd_plano'], array(7,9)))
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_instituidor_ativo();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 11)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_ieabprev();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 10)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_municipios();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 21)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_familia_corporativo();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 2)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_ceeeprev();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 22)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_ceranprev();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 23)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_foz();');
                    }

                    if(trim($row['tp_participante']) == 'ATIV' AND intval($row['cd_plano']) == 6)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_crmprev();');
                    }

                    if(trim($row['tp_participante']) == 'APOS' AND intval($row['cd_plano']) > 1)
                    { 
                        echo button_save('Gerar CSV', 'gerar_csv_aposentado();');
                    }
                }
        	}


        echo form_command_bar_detail_end();

        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>