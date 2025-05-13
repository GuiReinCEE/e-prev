<?php
    set_title('Regulamento Alteração - Responsável');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('cd_usuario')) ?>

    function cancelar()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao_responsavel/index') ?>";
    }

    function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao_responsavel/index') ?>";
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

    echo aba_start($abas);
    echo form_open('planos/regulamento_alteracao_responsavel/salvar');
        echo form_start_box('default_box', 'Cadastro');
            echo form_default_row('', 'Gerência:', $cd_gerencia['nome']);
            echo form_default_hidden('cd_gerencia', '', $cd_gerencia['cd_gerencia']);
            echo form_default_checkbox_group('usuario', 'Responsável:', $usuario, $usuario_responsavel, 150, 350);
        echo form_end_box('default_box');
        echo form_command_bar_detail_start();
            echo button_save('Salvar');
            if($cd_gerencia['nome'] != '')
            {
                echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
            }  
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(2);
    echo aba_end();

    $this->load->view('footer'); 

?>