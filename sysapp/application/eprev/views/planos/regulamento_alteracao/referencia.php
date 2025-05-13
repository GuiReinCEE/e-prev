<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/cadastro/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_artigo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_artigo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_unidade_basica()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$cd_regulamento_alteracao_artigo) ?>";
    }

    function ir_referencia_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/referencia_estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$artigo['cd_regulamento_alteracao_unidade_basica'].'/'.$cd_regulamento_alteracao_artigo) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function checkAll()
    {
        var ipts = $("#table-1 > tbody").find("input:checkbox");
        var check = document.getElementById("checkboxCheckAll");

        jQuery.each(ipts, function(){
            this.checked = check.checked ? true : false;

            salvar_item($(this).val(), check.checked ? 'S' : 'N');
        });
    }

    function check_item(t)
    {
        salvar_item(t.val(), t.is(':checked') ? 'S' : 'N');
    }

    function salvar_item(cd_regulamento_alteracao_unidade_basica, fl_checked)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/salvar_referencia') ?>",
        {
            cd_regulamento_alteracao                             : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_unidade_basica              : <?= $artigo['cd_regulamento_alteracao_unidade_basica'] ?>,
            cd_regulamento_alteracao_unidade_basica_referenciado : cd_regulamento_alteracao_unidade_basica,
            fl_salvar                                            : fl_checked
        },
        function(data){
            
        });
    }
</script>
<style>
    #artigo_item {
        white-space:normal !important;
    }
</style>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');

    if(trim($artigo['fl_artigo']) == 'N')
    {
        $abas[] = array('aba_unidade_basica', 'Unidade Básica', FALSE, 'ir_unidade_basica();');
    }

    $abas[] = array('aba_referencia', 'Referência', TRUE, 'location.reload();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $abas2[] = array('aba_referencia_estrutura', 'Estrutura', FALSE, 'ir_referencia_estrutura();');
    $abas2[] = array('aba_referencia_unidade_basica', 'Unidade Básica', TRUE, 'location.reload();');

    $head = array(
        '<input type="checkbox" id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">', 
        'Estrutura', 
        'Ordem', 
        'Descrição'
    );

    $body = array();

    $tags = array('<bi>', '</bi>', '<bu>', '</bu>', '<biu>', '</biu>', '<iu>', '</iu>', '<tab>');
    $subs = array('<b><i>', '</i></b>', '<b><u>', '</u></b>', '<b><i><u>', '</u></i></b>', '<i><u>', '</u></i>', '&nbsp;&nbsp;');

    foreach ($collection as $item)
    {
        if(trim($regulamento_alteracao['dt_alteracao_finalizada']) == '' OR (in_array($item['cd_regulamento_alteracao_unidade_basica'], $referenciado)))
        {
            $campo_check = array(
                'name'     => 'cd_unidade_basica_'.$item['cd_regulamento_alteracao_unidade_basica'],
                'id'       => 'cd_unidade_basica_'.$item['cd_regulamento_alteracao_unidade_basica'],
                'value'    => $item['cd_regulamento_alteracao_unidade_basica'],
                'checked'  => (in_array($item['cd_regulamento_alteracao_unidade_basica'], $referenciado) ? TRUE : FALSE),
                'onchange' => 'check_item($(this))'   
            );  

            $ds_unidade_basica = str_replace($tags, $subs, $item['ds_unidade_basica']);

            $body[] = array(
                form_checkbox($campo_check),
                array('<span class="'.$item['ds_class_label'].'">'.$item['ds_estrutura'].'</span>', 'text-align:jutify'),
                array($item['ds_ordem'], 'text-align:right'),
                array(nl2br($ds_unidade_basica), 'text-align:jutify')         
            );
        }
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    if(trim($regulamento_alteracao['dt_alteracao_finalizada']) != '')
    {
        $grid->col_oculta = array(0);
    }

    echo aba_start($abas);
        echo aba_start($abas2);
            echo form_start_box('default_box', 'Regulamento');
                echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
                echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
            echo form_end_box('default_box');
            echo form_start_box('default_box', (trim($artigo['fl_artigo']) == 'S' ? 'Artigo' : 'Unidade Básica'));
                echo form_default_row('', 'Estrutura:', $artigo['ds_estrutura']);
                echo form_default_row('artigo', (trim($artigo['fl_artigo']) == 'S' ? 'Artigo:' : 'Unidade Básica:'),  nl2br($artigo['ds_unidade_basica']));
            echo form_end_box('default_box');
            echo br();
            echo $grid->render();
            echo br(2);
        echo aba_end();
    echo aba_end();

    $this->load->view('footer_interna');
?>