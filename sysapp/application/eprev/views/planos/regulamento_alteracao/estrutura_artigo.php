<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
        'cd_regulamento_alteracao_estrutura',
		'nr_ordem',
		'ds_regulamento_alteracao_unidade_basica'
	),  'verifica_ordem()') ?>

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

    function cancelar()
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

    $(function(){
        default_tags_box_box_recolher();
    });

    function verifica_ordem()
    { 
    	if($("#cd_regulamento_alteracao_unidade_basica").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica') ?>",
			{
				cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				nr_ordem                 : $("#nr_ordem").val(),
                fl_verifica              : 'E'
			},
			function(data)
			{
                var confirmacao = "Salvar?";

				if(data.fl_ordem == "S")
				{
					confirmacao = 'Nº de ordem já existe.\n\n'+
					              'Deseja reordenar os itens já cadastrados?\n\n'+
		                          'Clique [Ok] para Sim\n\n'+
		                          'Clique [Cancelar] para Não\n\n';
				}

				if(confirm(confirmacao))
				{
					$("#fl_renumeracao").val("S");

					$("form").submit();
				}
				else
				{
					$("#fl_renumeracao").val("N");

					return false;
				}

			}, 'json');
    	}
    	else
    	{
    		$("#fl_renumeracao").val("N");

    		if(confirm("Salvar?"))
			{
				$("form").submit();
			}
    	}
    }

    function remover_unidade_basica(cd_regulamento_alteracao_unidade_basica, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica') ?>",
			{
				cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				nr_ordem                 : nr_ordem,
                fl_verifica              : 'E'
			},
        function(data)
			{
                var confirmacao = 'Deseja remover este artigo?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

				if(data.fl_ordem == "S")
				{
                    var confirmacao = 'Deseja remover o artigo e reordenar os outros itens?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
				}

                if(confirm(confirmacao))
                {
                    location.href = "<?= site_url('planos/regulamento_alteracao/remover_unidade_basica/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_unidade_basica+"/"+nr_ordem;
                }
				else
				{
					return false;
				}

			}, 'json');  
    }

    function excluir_unidade_basica(cd_regulamento_alteracao_unidade_basica, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica') ?>",
            {
                cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
                nr_ordem                 : nr_ordem,
                fl_verifica              : 'E'
            },
        function(data)
            {
                var confirmacao = 'Deseja excluir este artigo?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

                if(data.fl_ordem == "S")
                {
                    var confirmacao = 'Deseja excluir o artigo e reordenar os outros itens?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
                }

                if(confirm(confirmacao))
                {
                    location.href = "<?= site_url('planos/regulamento_alteracao/excluir_unidade_basica/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_unidade_basica+"/"+nr_ordem;
                }
                else
                {
                    return false;
                }

            }, 'json');  
    }

    function ir_unidade_basica()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$row['cd_regulamento_alteracao_unidade_basica']) ?>"
    }

    function ir_referencia()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/referencia/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$row['cd_regulamento_alteracao_unidade_basica']) ?>"
    }
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', TRUE, 'location.reload();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array(
        'Ordem',
        'Estrutura',
        'Artigo',
        'Qt. Unidade',
        'Qt. Ref.',
        '',
        '',
        ''
    );

    $body = array();

    $tags = array('<bi>', '</bi>', '<bu>', '</bu>', '<biu>', '</biu>', '<iu>', '</iu>', '<tab>');
    $subs = array('<b><i>', '</i></b>', '<b><u>', '</u></b>', '<b><i><u>', '</u></i></b>', '<i><u>', '</u></i>', '&nbsp;&nbsp;');

    foreach ($collection as $item)
    {
        $ds_artigo = str_replace($tags, $subs, $item['ds_artigo']);

        $body[] = array(
            array($item['nr_ordem'], 'text-align:right'),
            array('<span class="'.$item['ds_class_label'].'">'.$item['ds_estrutura'].'</span>', 'text-align:jutify'),
            array(nl2br($ds_artigo), 'text-align:jutify'),
            '<span class="badge badge-warning">'.$item['qt_unidade_basica'].'</span>',
            '<span class="badge badge-warning">'.$item['qt_referencia'].'</span>',
            anchor('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[unidade básica]'),
            anchor('planos/regulamento_alteracao/referencia/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[ref]'),
            (trim($regulamento_alteracao['dt_alteracao_finalizada']) == '' ?             
            anchor('planos/regulamento_alteracao/estrutura_artigo/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[editar]').' '.
            '<a href="javascript:void(0)" 
            onclick="remover_unidade_basica('.intval($item['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['nr_ordem']).')">[remover]</a>
            <a href="javascript:void(0)" 
            onclick="excluir_unidade_basica('.intval($item['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['nr_ordem']).')">[excluir]</a>'
            : '')
        );
    }
    
    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    if(trim($regulamento_alteracao['dt_alteracao_finalizada']) != '')
    {
        $grid->col_oculta = array(7);
    }

    echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');
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

        if(trim($regulamento_alteracao['dt_alteracao_finalizada']) == '')
        {
            echo form_open('planos/regulamento_alteracao/salvar_estrutura_artigo');
                echo form_start_box('default_estrutura_box', 'Cadastro');
                    echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']); 
                    echo form_default_hidden('cd_regulamento_alteracao_unidade_basica', '', $row['cd_regulamento_alteracao_unidade_basica']);
                    echo form_default_hidden('fl_renumeracao', '', 'N');  

                    if(intval($row['cd_regulamento_alteracao_unidade_basica']) == 0)
                    {
                        echo form_default_dropdown('cd_regulamento_alteracao_estrutura', 'Estrutura: (*)', $estrutura, $row['cd_regulamento_alteracao_estrutura']);
                        echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
                    }
                    else
                    {
                        echo form_default_hidden('cd_regulamento_alteracao_estrutura', '', $row['cd_regulamento_alteracao_estrutura']);
                        echo form_default_hidden('nr_ordem', '', $row);

                        echo form_default_row('', 'Estrutura:', $row['ds_estrutura']);
                        echo form_default_row('', 'Ordem:', $row['nr_ordem']);
                    }
                    
                    echo form_default_textarea('ds_regulamento_alteracao_unidade_basica', 'Descrição: (*)', $row, 'style="height:150px;"');
                echo form_end_box('default_estrutura_box');
                echo form_command_bar_detail_start();
                    echo button_save('Salvar');
                    if(intval($row['cd_regulamento_alteracao_unidade_basica']) > 0)
	                {	
                        echo button_save('Unidade Básica', 'ir_unidade_basica();', 'botao_verde');
                        echo button_save('Referência', 'ir_referencia();', 'botao_verde');
	                	echo button_save('Cancelar', 'cancelar();', 'botao_disabled');
	                } 
                echo form_command_bar_detail_end();
            echo form_close();   
        }  
        
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>