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
                echo form_default_row('dt_liberacao_atuarial', 'Dt. Atuarial:',  '<span class="label label-important">'.$row['dt_liberacao_atuarial'].'</span>');
                echo form_default_row('usuario_atuarial', 'Usuário Atuarial:',  '<span class="label label-important">'.$row['usuario_atuarial'].'</span>');
            }

            if(trim($row['dt_liberacao_comunicacao']) != '')
            {
                echo form_default_row('dt_liberacao_comunicacao', 'Dt. Liberação Comunicação:', '<span class="label label-success">'.$row['dt_liberacao_comunicacao'].'</span>');
                echo form_default_row('usuario_comunicacao', 'Usuário Comunicação:', '<span class="label label-success">'.$row['usuario_comunicacao'].'</span>');
            }
        echo form_end_box('default_box');
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>