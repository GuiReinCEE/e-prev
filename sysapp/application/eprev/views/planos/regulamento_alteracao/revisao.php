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

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function check_item(t)
    {
        salvar_item(t.val(), t.is(':checked') ? 'S' : 'N');
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function salvar_item(cd_regulamento_alteracao_revisao, fl_checked)
    {
        var verificacao;

        $.post("<?= site_url('planos/regulamento_alteracao/salvar_verificado') ?>",
        {
            cd_regulamento_alteracao         : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_revisao : cd_regulamento_alteracao_revisao,
            fl_verificado                    : fl_checked
        },
        function(data){

            $.each(data, function(key, value) {
                verificacao = '';

                if(value != 'null')
                {
                    verificacao = value;
                }

                $("#span_verificaca_"+key).html(verificacao);
            });
        }, 'json');
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', TRUE, 'location.reload();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array( 
        'Ordem',
        'Descrição',
        'Dt. Verificação',
        ''
    );

    $body = array();

    foreach ($collection as $key => $item) 
    {
        $campo_check = array(
            'name'     => 'cd_regulamento_alteracao_revisao_'.$item['cd_regulamento_alteracao_revisao'],
            'id'       => 'cd_regulamento_alteracao_revisao_'.$item['cd_regulamento_alteracao_revisao'],
            'value'    => $item['cd_regulamento_alteracao_revisao'],
            'checked'  => (trim($item['dt_verificado']) != '' ? TRUE : FALSE),
            'onchange' => 'check_item($(this))'   
        ); 

        $check = '';

        if(intval($item['tl_filho']) == 0)
        {
            $check = form_checkbox($campo_check);
        }

    	$body[] = array(
            array($item['ds_ordem'], 'text-align:right;'),
            array($item['ds_regulamento_alteracao_revisao'], 'text-align:left;'),
            '<span id="span_verificaca_'.$item['cd_regulamento_alteracao_revisao'].'">'.$item['dt_verificado'].'</span>',
            $check
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_start_box('default_box', 'Regulamento');
			echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
			echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');

		echo br();
        echo $grid->render();
        echo br();
    echo aba_end();

    $this->load->view('footer_interna');
?>