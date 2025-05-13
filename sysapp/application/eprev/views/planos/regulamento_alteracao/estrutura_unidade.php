<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
        'cd_regulamento_alteracao_unidade_basica_pai',
        'cd_regulamento_alteracao_estrutura_tipo',
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

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function cancelar()
    {
    	location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$artigo['cd_regulamento_alteracao_unidade_basica']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function set_unidade_basica_pai(cd_regulamento_alteracao_unidade_basica_pai)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/set_unidade_basica_pai') ?>",
        {
            cd_regulamento_alteracao                    : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_unidade_basica_pai : cd_regulamento_alteracao_unidade_basica_pai
        },
        function(data)
        {
            $("#nr_ordem").val(data.nr_ordem);
            $("#cd_regulamento_alteracao_estrutura_tipo").val(data.cd_regulamento_alteracao_estrutura_tipo);

            $("#ds_regulamento_alteracao_unidade_basica").focus();
        }, 'json'); 
    }

    function set_unidade_basica_tipo(cd_regulamento_alteracao_estrutura_tipo)
    {
        var cd_regulamento_alteracao_unidade_basica_pai = $("#cd_regulamento_alteracao_unidade_basica_pai").val();

        if(cd_regulamento_alteracao_unidade_basica_pai != '')
        {
            $.post("<?= site_url('planos/regulamento_alteracao/set_unidade_basica_tipo') ?>",
        {
            cd_regulamento_alteracao                    : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_unidade_basica_pai : cd_regulamento_alteracao_unidade_basica_pai,
            cd_regulamento_alteracao_estrutura_tipo     : cd_regulamento_alteracao_estrutura_tipo
        },
        function(data)
        {
            $("#nr_ordem").val(data.nr_ordem);

            $("#ds_regulamento_alteracao_unidade_basica").focus();
        }, 'json'); 
        }
        else
        {
            alert("Informe a Unidade Pai");
        }
    }

    $(function(){
        default_tags_box_box_recolher();
    });

    function verifica_ordem()
    { 
    	if($("#cd_regulamento_alteracao_unidade_basica").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica_filho') ?>",
			{
				cd_regulamento_alteracao                    : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				cd_regulamento_alteracao_unidade_basica_pai : $("#cd_regulamento_alteracao_unidade_basica_pai").val(),
				cd_regulamento_alteracao_estrutura_tipo     : $("#cd_regulamento_alteracao_estrutura_tipo").val(),
				nr_ordem                                    : $("#nr_ordem").val(),
                fl_verifica                                 : 'R'
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

    function remover_unidade_basica_filho(cd_regulamento_alteracao_unidade_basica, nr_ordem, cd_regulamento_alteracao_unidade_basica_pai, cd_regulamento_alteracao_unidade_basica_artigo, cd_regulamento_alteracao_estrutura_tipo)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica_filho') ?>",
			{
				cd_regulamento_alteracao                    : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
                cd_regulamento_alteracao_unidade_basica_pai : cd_regulamento_alteracao_unidade_basica_pai,
                cd_regulamento_alteracao_estrutura_tipo     : cd_regulamento_alteracao_estrutura_tipo,
				nr_ordem                                    : nr_ordem,
                fl_verifica                                 : 'E'
			},
        function(data)
			{
                var confirmacao = 'Deseja remover a unidade basica?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

				if(data.fl_ordem == "S")
				{
                    var confirmacao = 'Deseja remover a unidade basica e reordenar os outros itens?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
				}

                if(confirm(confirmacao))
                {
                    location.href = "<?= site_url('planos/regulamento_alteracao/remover_unidade_basica_filho/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_unidade_basica+'/'+nr_ordem+'/'+cd_regulamento_alteracao_unidade_basica_pai+'/'+cd_regulamento_alteracao_unidade_basica_artigo+'/'+cd_regulamento_alteracao_estrutura_tipo;
                }
				else
				{
					return false;
				}

			}, 'json');  
    }

    function excluir_unidade_basica_filho(cd_regulamento_alteracao_unidade_basica, nr_ordem, cd_regulamento_alteracao_unidade_basica_pai, cd_regulamento_alteracao_unidade_basica_artigo, cd_regulamento_alteracao_estrutura_tipo)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_unidade_basica_filho') ?>",
            {
                cd_regulamento_alteracao                    : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
                cd_regulamento_alteracao_unidade_basica_pai : cd_regulamento_alteracao_unidade_basica_pai,
                cd_regulamento_alteracao_estrutura_tipo     : cd_regulamento_alteracao_estrutura_tipo,
                nr_ordem                                    : nr_ordem,
                fl_verifica                                 : 'E'
            },
        function(data)
            {
                var confirmacao = 'Deseja excluir a unidade basica?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

                if(data.fl_ordem == "S")
                {
                    var confirmacao = 'Deseja excluir a unidade basica e reordenar os outros itens?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
                }

                if(confirm(confirmacao))
                {
                    location.href = "<?= site_url('planos/regulamento_alteracao/excluir_unidade_basica_filho/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_unidade_basica+'/'+nr_ordem+'/'+cd_regulamento_alteracao_unidade_basica_pai+'/'+cd_regulamento_alteracao_unidade_basica_artigo+'/'+cd_regulamento_alteracao_estrutura_tipo;
                }
                else
                {
                    return false;
                }

            }, 'json');  
    }

    function ir_referencia()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/referencia/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$row['cd_regulamento_alteracao_unidade_basica']) ?>"
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
    $abas[] = array('aba_unidade_basica', 'Unidade Básica', TRUE, 'location.reload();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array(
        'Ordem',
        'Tipo',
        'Unidade Básica',
        'Qt. Ref.',
        '',
        ''
    );

    $body = array();

    $tags = array('<bi>', '</bi>', '<bu>', '</bu>', '<biu>', '</biu>', '<iu>', '</iu>', '<tab>');
    $subs = array('<b><i>', '</i></b>', '<b><u>', '</u></b>', '<b><i><u>', '</u></i></b>', '<i><u>', '</u></i>', '&nbsp;&nbsp;');

    foreach ($collection as $item)
    {
        $ds_unidade_basica = str_replace($tags, $subs, $item['ds_unidade_basica']);

        $body[] = array(
            array($item['ds_ordem'], 'text-align:right'),
            array($item['ds_regulamento_alteracao_estrutura_tipo'], 'text-align:left'),
            array(nl2br($ds_unidade_basica), 'text-align:jutify'),
            '<span class="badge badge-warning">'.$item['qt_referencia'].'</span>',
            anchor('planos/regulamento_alteracao/referencia/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_unidade_basica'].'/'.$artigo['cd_regulamento_alteracao_unidade_basica'], '[ref]'),
            (trim($regulamento_alteracao['dt_alteracao_finalizada']) == '' ?             
            anchor('planos/regulamento_alteracao/estrutura_unidade/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$artigo['cd_regulamento_alteracao_unidade_basica'].'/'.$item['cd_regulamento_alteracao_unidade_basica'], '[editar]').' '.
            '<a href="javascript:void(0)" 
            onclick="remover_unidade_basica_filho('.intval($item['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['nr_ordem']).', '.intval($item['cd_regulamento_alteracao_unidade_basica_pai']).', '.intval($artigo['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['cd_regulamento_alteracao_estrutura_tipo']).')">[remover]</a>
            <a href="javascript:void(0)" 
            onclick="excluir_unidade_basica_filho('.intval($item['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['nr_ordem']).', '.intval($item['cd_regulamento_alteracao_unidade_basica_pai']).', '.intval($artigo['cd_regulamento_alteracao_unidade_basica']).', '.intval($item['cd_regulamento_alteracao_estrutura_tipo']).')">[excluir]</a>'
            : '')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    if(trim($regulamento_alteracao['dt_alteracao_finalizada']) != '')
    {
        $grid->col_oculta = array(5);
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
        if($regulamento_alteracao['dt_alteracao_finalizada'] == '')
        {
            echo form_open('planos/regulamento_alteracao/salvar_estrutura_unidade');
                echo form_start_box('default_estrutura_box', 'Cadastro');
                    echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']);
                    echo form_default_hidden('cd_regulamento_alteracao_estrutura', '', $artigo['cd_regulamento_alteracao_estrutura']);
                    echo form_default_hidden('cd_regulamento_alteracao_unidade_basica_artigo', '', $artigo['cd_regulamento_alteracao_unidade_basica']);
                    echo form_default_hidden('cd_regulamento_alteracao_unidade_basica', '', $row['cd_regulamento_alteracao_unidade_basica']); 
                    echo form_default_hidden('fl_renumeracao', '', 'N'); 

                    echo form_default_row('', 'Estrutura:', '<span class="'.$artigo['ds_class_label'].'">'.$artigo['ds_estrutura'].'</span>');
                    echo form_default_row('artigo', 'Artigo:', nl2br($artigo['ds_artigo']));

                    
                    if(intval($row['cd_regulamento_alteracao_unidade_basica']) == 0)
                    {
                        echo form_default_dropdown('cd_regulamento_alteracao_unidade_basica_pai', 'Unidade Pai: (*)', $unidade_basica, $row['cd_regulamento_alteracao_unidade_basica_pai'], 'onchange="set_unidade_basica_pai($(this).val())"');
                        echo form_default_dropdown('cd_regulamento_alteracao_estrutura_tipo', 'Tipo: (*)', $estrutura_tipo, $row['cd_regulamento_alteracao_estrutura_tipo'], 'onchange="set_unidade_basica_tipo($(this).val())"');
                        echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
                    }
                    else
                    {
                        echo form_default_hidden('cd_regulamento_alteracao_unidade_basica_pai', '', $row['cd_regulamento_alteracao_unidade_basica_pai']);
                        echo form_default_hidden('cd_regulamento_alteracao_estrutura_tipo', '', $row['cd_regulamento_alteracao_estrutura_tipo']);
                        echo form_default_hidden('nr_ordem', '', $row);

                        echo form_default_row('', 'Unidade Pai:', $row['ds_regulamento_alteracao_unidade_basica_pai']);
                        echo form_default_row('', 'Tipo:', $row['ds_regulamento_alteracao_estrutura_tipo']);
                        echo form_default_row('', 'Ordem:', $row['nr_ordem']);
                        
                    }
                    
                    echo form_default_textarea('ds_regulamento_alteracao_unidade_basica', 'Descrição: (*)', $row, 'style="height:150px;"');

                echo form_end_box('default_estrutura_box');
                echo form_command_bar_detail_start();
                    echo button_save('Salvar');
                    if(intval($row['cd_regulamento_alteracao_unidade_basica']) > 0)
	                {	
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