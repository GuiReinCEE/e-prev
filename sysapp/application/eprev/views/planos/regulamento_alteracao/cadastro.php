<?php
    set_title('Regulamento de Plano');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()) ?>

    function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_artigo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_artigo/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$row['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$row['cd_regulamento_alteracao']) ?>";
    }

	function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$row['cd_regulamento_alteracao']) ?>";
	}

    function finalizar_alteracoes()
    {
        var confirmacao = 'Deseja finalizar as alterações do regulamento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('planos/regulamento_alteracao/finalizar_alteracoes/'.$row['cd_regulamento_alteracao']) ?>";
        }      
    }

    function salvar_regualamento_alteracao()
    {
        var confirmacao = 'Deseja criar uma nova alteração de regulamento?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('planos/regulamento_alteracao/salvar_regualamento_alteracao/'.$row['cd_regulamento_alteracao']) ?>";
        }      
    }

    $(function(){
        default_tags_box_box_recolher();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');
    
    echo aba_start($abas);
        echo form_open('planos/regulamento_alteracao/salvar');
             echo form_start_box('default_tags_box', 'TAGS para o texto');
                echo form_default_row('', 'NEGRITO:', '&lt;b&gt;TEXTO&lt;/b&gt; = <b>TEXTO</b>');
                echo form_default_row('', 'NEGRITO E ITÁLICO:', '&lt;bi&gt;TEXTO&lt;/bi&gt; = <b><i>TEXTO</i></b>');
                echo form_default_row('', 'NEGRITO E SUBLINHADO:', '&lt;bu&gt;TEXTO&lt;/bu&gt; = <b><u>TEXTO</u></b>');
                echo form_default_row('', 'NEGRITO, ITÁLICO E SUBLINHADO:', '&lt;biu&gt;TEXTO&lt;/biu&gt; = <b><u><i>TEXTO</i></u></b>');
                echo form_default_row('', 'ITÁLICO:', '&lt;i&gt;TEXTO&lt;/i&gt; = <i>TEXTO</i>');
                echo form_default_row('', 'ITÁLICO E SUBLINHADO:', '&lt;iu&gt;TEXTO&lt;/iu&gt; = <u><i>TEXTO</i></u>');
                echo form_default_row('', 'SUBLINHADO:', '&lt;u&gt;TEXTO&lt;/u&gt; = <u>TEXTO</u>');
                echo form_default_row('', 'TABULAÇÃO:', '&lt;tab&gt;TEXTO = &nbsp;&nbsp;TEXTO');
            echo form_end_box('default_tags_box');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_regulamento_alteracao', '', $row['cd_regulamento_alteracao']);
                echo form_default_row('', 'Regulamento:', $row['ds_regulamento_tipo']);
                echo form_default_row('', 'CNPB:', $row['ds_cnpb']);
                echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
                
                if(intval($row['dt_alteracao_finalizada'] != ''))
                {               
                    echo form_default_row('', 'Dt. Finalizado:', $row['dt_alteracao_finalizada']);
                }
                
                echo form_default_date('dt_envio_previc', 'Dt. Envio PREVIC:', $row);
                echo form_default_date('dt_aprovacao_previc', 'Dt. Aprovação PREVIC:', $row);
                echo form_default_text('ds_aprovacao_previc', 'Descrição Doc. PREVIC:', $row, 'style="width:700px;"');
                echo form_default_upload_iframe('arquivo', 'regulamento', 'Doc. Aprovação:', array($row['arquivo'], $row['arquivo_nome']), 'arquivo_nome', true);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar');

                if(intval($row['dt_alteracao_finalizada'] == ''))
                {
                    echo button_save('Finalizar Alterações', 'finalizar_alteracoes();', 'botao_vermelho');
                }
                else if (intval($row['fl_nova_versao'] == 0))
                {
                    echo button_save('Nova Versão', 'salvar_regualamento_alteracao();');
                }
            echo form_command_bar_detail_end();
        echo form_close();     
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>