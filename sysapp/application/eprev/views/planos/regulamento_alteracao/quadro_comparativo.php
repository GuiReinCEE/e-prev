<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'cd_regulamento_alteracao_quadro_comparativo',
		'nr_ordem'
	), 'verifica_ordem()') ?>

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

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function cancelar()
    {
    	location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function iniciar()
    {
    	var confirmacao = 'Deseja iniciar as alterações do quadro comparativo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('planos/regulamento_alteracao/iniciar_quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
		}
    }

    function verifica_ordem()
    {
    	if($("#cd_regulamento_alteracao_quadro_comparativo").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_quadro_comparativo') ?>",
			{
				cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				nr_ordem                 : $("#nr_ordem").val(),
                fl_verifica              : 'R'
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

    function excluir_quadro_comparativo(cd_regulamento_alteracao_quadro_comparativo, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_quadro_comparativo') ?>",
		{
			cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
			nr_ordem                 : nr_ordem,
            fl_verifica              : 'E'
		},
        function(data)
		{
            var confirmacao = 'Deseja remover o item?\n\n'+
                      'Clique [Ok] para Sim\n\n'+
                      'Clique [Cancelar] para Não\n\n';

			if(data.fl_ordem == "S")
			{
                var confirmacao = 'Deseja remover o item e reordenar os outros itens?\n\n'+
                      'Clique [Ok] para Sim\n\n'+
                      'Clique [Cancelar] para Não\n\n';
			}

            if(confirm(confirmacao))
            {
                location.href = "<?= site_url('planos/regulamento_alteracao/excluir_quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_quadro_comparativo+"/"+nr_ordem;
            }
			else
			{
				return false;
			}

		}, 'json');  
    }

    function finalizar()
    {
    	var confirmacao = 'Deseja finalizar as alterações do quadro comparativo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('planos/regulamento_alteracao/finalizar_quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
		}
	}

	function ir_atividade(cd_regulamento_alteracao, cd_regulamento_alteracao_unidade_basica)
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividade') ?>/"+ cd_regulamento_alteracao + '/' + cd_regulamento_alteracao_unidade_basica;
	}

	function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

	function encaminhar_atividades()
	{
		var text = "Deseja encaminhar todas as atividades?\n\n"+
		"[OK] para Sim\n\n"+
		"[Cancelar] para Não";

		if(confirm(text))
		{
			location.href = "<?= site_url('planos/regulamento_alteracao/encaminhar_atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
		}
	}

    $(function(){
        default_tags_box_box_recolher();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', TRUE, 'location.reload();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array( 
        'Ordem',
		(trim($regulamento_alteracao['dt_aprovacao_previc']) != '' ? 'Texto Anterior' : 'Texto Atual').'<br />('.$ds_rodape_anterior.')',
		(trim($regulamento_alteracao['dt_aprovacao_previc']) != '' ? 'Texto Atual<br />('.$regulamento_alteracao['ds_rodape'].')' : 'Texto Proposto'),
        (trim($regulamento_alteracao['dt_inicio_quadro_comparativo']) == '' ? 'Ajustes' : 'Justificativas'),
        'Atividade',
        ''
    );

    $body = array();
    
    foreach ($collection as $key => $item) 
    {
    	switch ($item['tp_align_anterior']) 
    	{
		    case 'C':
		        $ds_align_anterior = 'center';
		        break;
		    case 'R':
		        $ds_align_anterior = 'right';
		        break;
		    case 'J':
		        $ds_align_anterior = 'justify';
		        break;
		    default:
       			$ds_align_anterior = 'left';
		}

		switch ($item['tp_align_atual']) 
    	{
		    case 'C':
		        $ds_align_atual = 'center';
		        break;
		    case 'R':
		        $ds_align_atual = 'right';
		        break;
		    case 'J':
		        $ds_align_atual = 'justify';
		        break;
		    default:
		        $ds_align_atual = 'left';
		}

		$link = '';

		if(intval($item['cd_regulamento_alteracao_quadro_comparativo']) > 0 AND trim($regulamento_alteracao['dt_fim_quadro_comparativo']) == '')
		{
			if(intval($item['cd_regulamento_alteracao_unidade_basica']) > 0)
            {
            	$link = '<a href="javascript:void(0)" 
            	onclick="ir_atividade('.intval($regulamento_alteracao['cd_regulamento_alteracao']).', '.intval($item['cd_regulamento_alteracao_unidade_basica']).')">[atividade]</a><br>';
            }

			$link .= anchor('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_quadro_comparativo'], '[editar]').' '.
            '<br><a href="javascript:void(0)" 
            onclick="excluir_quadro_comparativo('.intval($item['cd_regulamento_alteracao_quadro_comparativo']).', '.intval($item['nr_ordem']).')">[excluir]</a>';
		}

    	$body[] = array(
            array($item['nr_ordem'], 'text-align:right'),
            array($item['ds_texto_anterior'], 'text-align:'.$ds_align_anterior),
            array($item['ds_texto_atual'], 'text-align:'.$ds_align_atual),
            array(nl2br($item['ds_justificativa']), 'text-align:jutify'),
            $item['dt_envio'],
            $link
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
    	if(trim($regulamento_alteracao['dt_inicio_quadro_comparativo']) == '')
    	{
    		echo br();
	    	echo '<center>'.button_save('Iniciar Alterações', 'iniciar();', 'botao_verde').'</center>';
    	}
    	else if(trim($regulamento_alteracao['dt_fim_quadro_comparativo']) == '')
    	{
    		$align = array(
    			array('value' => 'C', 'text' => 'Centralizado'),
    			array('value' => 'J', 'text' => 'Justificado'),
    			array('value' => 'R', 'text' => 'Direita'),
    			array('value' => 'L', 'text' => 'Esquerda')
    		);

    		echo form_open('planos/regulamento_alteracao/salvar_quadro_comparativo');
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
	            echo form_start_box('default_atividade_box', 'Atividade');
	            	echo form_default_row('', 'Qt. Atividades Cadastradas:', '<badge class="badge badge-success">'.$atividade['qt_atividade_cadastrada'].'</badge>');
	            	echo form_default_row('', 'Qt. Atividades Encaminhadas:', '<badge class="badge badge-info">'.$atividade['qt_atividade_encaminhada'].'</badge>');
	            echo form_end_box('default_atividade_box');
	            echo form_start_box('default_cadastro_box', 'Cadastro');
	                echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']);
	                echo form_default_hidden('cd_regulamento_alteracao_quadro_comparativo', '', $row['cd_regulamento_alteracao_quadro_comparativo']);
	                echo form_default_hidden('fl_renumeracao', '', 'N'); 

	                if(intval($row['cd_regulamento_alteracao_quadro_comparativo']) == 0)
	                {
	                	echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
	                }
	                else
	                {
	                	echo form_default_row('', 'Ordem:', $row['nr_ordem']);
	                	echo form_default_hidden('nr_ordem', '', $row['nr_ordem']);
	                }
	                
	                echo form_default_textarea('ds_texto_anterior', 'Texto Anterior:', $row, 'style="height:150px;"');
	                echo form_default_dropdown('tp_align_anterior', 'Alinhamento do Texto Anterior:', $align, $row['tp_align_anterior']);
	                echo form_default_textarea('ds_texto_atual', 'Texto Atual:', $row, 'style="height:150px;"');
	                echo form_default_dropdown('tp_align_atual', 'Alinhamento do Texto Atual:', $align, $row['tp_align_atual']);
	           		echo form_default_textarea('ds_justificativa', 'Justificativa:', $row, 'style="height:150px;"');
	            echo form_end_box('default_cadastro_box');
	            echo form_command_bar_detail_start();
	                echo button_save('Salvar');
	                if(intval($row['cd_regulamento_alteracao_quadro_comparativo']) > 0)
	                {	
	                	echo button_save('Cancelar', 'cancelar();', 'botao_disabled');
	                } 

	                if(trim($regulamento_alteracao['dt_fim_quadro_comparativo']) == '')
	                {
	                	echo button_save('Finalizar Alterações', 'finalizar();', 'botao_vermelho');

	                	if(intval($atividade['qt_atividade_cadastrada']) > intval($atividade['qt_atividade_encaminhada']))
	                	{
	                		echo button_save('Encaminhar Atividades', 'encaminhar_atividades();', 'botao_verde');
	                	}
	                }
	            echo form_command_bar_detail_end();
	        echo form_close();     
    	}

    	echo br();
        echo $grid->render();
        echo br();
    echo aba_end();

    $this->load->view('footer_interna');
?>