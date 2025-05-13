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

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function check_item(t)
    {
    	var retorno = t.val().split("_");

        salvar_item(retorno[0], retorno[1], t.is(':checked') ? 'S' : 'N');
    }

    function salvar_item(tipo, cd, fl_checked)
    {
    	var verificacao;

        $.post("<?= site_url('planos/regulamento_alteracao/salvar_verificado_remissao') ?>",
        {
            cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_ref                   : cd,
            fl_tipo                  : tipo,
            fl_verificado            : fl_checked
        },
        function(data){
            verificacao = '';

            if(data != 'null')
            {
                verificacao = data;
            }

            $("#span_verificaca_"+tipo+"_"+cd).html(verificacao);
        }, 'json');
    }

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', TRUE, 'location.reload();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array( 
        'Artigo/Unidade Básica',
        'Referenciado',
        'Dt. Verificação',
        ''
    );

    $body = array();

    foreach ($collection as $key => $item) 
    {
    	$campo_check = array(
            'name'     => 'cd_ref_'.$item['fl_tipo'].'_'.$item['cd_ref'],
            'id'       => 'cd_ref_'.$item['fl_tipo'].'_'.$item['cd_ref'],
            'value'    => $item['fl_tipo'].'_'.$item['cd_ref'],
            'checked'  => (trim($item['dt_verificado']) != '' ? TRUE : FALSE),
            'onchange' => 'check_item($(this))'   
        ); 

    	$ds_item = '<span class="'.$item['ds_class_label'].'">'.$item['ds_estrutura'].'</span>'.br(2);

        if(trim($item['ds_artigo_pai']) != '')
        {
        	$ds_item .=	$item['ds_artigo_pai'].br(2);
        }

        $ds_item .= $item['ds_unidade_basica'];

        if(trim($collection[$key]['fl_artigo']) == 'N')
        {
        	$ds_link = anchor('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica_pai'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[editar]');
        }
        else
        {
        	$ds_link = anchor('planos/regulamento_alteracao/estrutura_artigo/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[editar]');
        }

        $table = '<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">';
        
        foreach ($item['estrutura'] as $key2 => $item2) 
    	{
    		$ds_status = '';
    		$ds_renumeracao = '';

    		if(trim($item2['dt_removido']) != '')
    		{
    			$ds_status = 'Removido';
    		}
    		else if(trim($item2['fl_alteracao_ordem']) == 'S')
    		{
    			$ds_status = 'Renumerado';
    			$ds_renumeracao = 'De: '.$item2['ds_alteracao_referencia'].br().' Para: '.$item2['ds_ordem'];
    		}

    		$table .= '<tr>';
    		$table .= '<td style="text-align:justify; width:70%;">';
    		$table .= '<span class="'.$item2['ds_class_label'].'">'.$item2['ds_estrutura'].'</span>';
    		$table .= '</td>';
			$table .= '<td><span class="label label-important">'.$ds_status.'</span></td>';
			$table .= '<td>'.$ds_renumeracao.'</td>';
			$table .= '</tr>';
    	}

    	foreach ($item['unidade_basica'] as $key2 => $item2) 
    	{
    		$ds_status = '';
    		$ds_renumeracao = '';

    		if(trim($item2['dt_removido']) != '')
    		{
    			$ds_status = 'Removido';
    		}
    		else if(trim($item2['fl_alteracao_ordem']) == 'S')
    		{
    			$ds_status = 'Renumerado';
    			$ds_renumeracao = 'De: '.$item2['ds_alteracao_referencia'].br().' Para: '.$item2['ds_ordem'];
    		}

    		$table .= '<tr>';
    		$table .= '<td style="text-align:justify;">';
    		$table .= nl2br($item2['ds_unidade_basica']);
    		$table .= '</td>';
			$table .= '<td><span class="label label-important">'.$ds_status.'</span></td>';
			$table .= '<td>'.$ds_renumeracao.'</td>';
			$table .= '</tr>';
    	}
        
        $table .= '</table>';

        $body[] = array(
            array(nl2br($ds_item).br(2).$ds_link, 'text-align:justify; width:100px;'),
            array($table, 'text-align:left; width:55%;'),
            array('<span id="span_verificaca_'.$item['fl_tipo'].'_'.$item['cd_ref'].'">'.$item['dt_verificado'].'</span>', 'text-align:center; width:5%;'),
            array(form_checkbox($campo_check), 'text-align:center; width:5%;')
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