<?php
    set_title('Processos');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('dt_ini_vigencia', 'procedimento', 'cod_responsavel')) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('gestao/processo') ?>";
    }

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/processo/indicador/'.$row['cd_processo']) ?>";
    }

    function ir_instrumento()
    {
        location.href = "<?= site_url('gestao/processo/instrumento/'.$row['cd_processo']) ?>";
    }

    function ir_fluxo()
    {
        location.href = "<?= site_url('gestao/processo/fluxo/'.$row['cd_processo']) ?>";
    }

    function ir_pop()
    {
        location.href = "<?= site_url('gestao/processo/pop/'.$row['cd_processo']) ?>";
    }

    function ir_registro()
    {
        location.href = "<?= site_url('gestao/processo/registro/'.$row['cd_processo']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('gestao/processo/revisao_historico/'.$row['cd_processo']) ?>";
    }
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    if(intval($row['cd_processo']) > 0)
    {
        $abas[] = array('aba_indicador', 'Indicadores', FALSE, 'ir_indicador();');
        $abas[] = array('aba_instrumento', 'IT\'s', FALSE, 'ir_instrumento();');
        $abas[] = array('aba_fluxo', 'Fluxograma', FALSE, 'ir_fluxo();');
        $abas[] = array('aba_pop', 'POP', FALSE, 'ir_pop();');
        $abas[] = array('aba_registros', 'Registros', FALSE, 'ir_registro();');
        $abas[] = array('aba_revisao', 'Histórico de Revisões', FALSE, 'ir_revisao();');
    }

    $versao_it = array(
        array('value' => 'S', 'text' => 'Sim'), 
        array('value' => 'N', 'text' => 'Não')
    );

    echo aba_start($abas);
        echo form_open('gestao/processo/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_processo', '', $row['cd_processo']);
                echo form_default_date('dt_ini_vigencia', 'Dt Início Vigência: (*)', $row);
                echo form_default_date('dt_fim_vigencia', 'Dt Final Vigência:', $row);
    			echo form_default_text('procedimento', 'Descrição: (*)', $row, 'style="width:500px;"');
                echo form_default_dropdown('fl_versao_it', 'Novo Modelo IT: (*)', $versao_it, $row['fl_versao_it']);
    			echo form_default_dropdown('cod_responsavel', 'Responsável: (*)', $responsavel, $row['cod_responsavel']);

                if(intval($row['cd_processo']) > 0)
                {
                    echo form_default_checkbox_group('usuario_responsavel', 'Usuário Envolvidos:', $usuario_gerencia, $usuario_responsavel, 120);
                }

    			echo form_default_checkbox_group('gerencia_envolvida', 'Envolvidos:', $responsavel, $gerencia_envolvida, 190);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();     
    			echo button_save('Salvar');
            echo form_command_bar_detail_end();
        echo form_close();
        echo br(2);	
    echo aba_end();

    $this->load->view('footer_interna');
?>