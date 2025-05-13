<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'nr_ordem',
		'ds_regulamento_alteracao_glossario'
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

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_versao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/versao_anterior/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function verifica_ordem()
    { 
    	if($("#cd_regulamento_alteracao_glossario").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_glossario') ?>",
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

    function remover(cd_regulamento_alteracao, cd_regulamento_alteracao_glossario, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_glossario') ?>",
			{
				cd_regulamento_alteracao : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				nr_ordem                 : nr_ordem,
                fl_verifica              : 'E'
			},
        function(data)
			{
                var confirmacao = 'Deseja remover este item?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

				if(data.fl_ordem == "S")
				{
					var confirmacao = 'Deseja remover este item e reordenar o restante?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';
				}

				if(confirm(confirmacao))
				{
					location.href = "<?= site_url('planos/regulamento_alteracao/remover_glossario') ?>/"+cd_regulamento_alteracao+"/"+cd_regulamento_alteracao_glossario+"/"+nr_ordem;
				}
				else
				{
					return false;
				}

			}, 'json');        
    }

    $(function(){
        default_tags_box_box_recolher();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', TRUE, 'location.reload();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

    $head = array(
        'Ordem',
        'Descrição',
        ''
    );

    $body = array();

    foreach ($collection as $item)
    {
        $editar = anchor('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_glossario'], '[editar]');

        $body[] = array(
            $item['nr_ordem'],
            array($item['ds_regulamento_alteracao_glossario'], 'style="justify"'),
            $editar.br().'<a href="javascript:void(0)" 
            onclick="remover('.intval($regulamento_alteracao['cd_regulamento_alteracao']).', '.intval($item['cd_regulamento_alteracao_glossario']).', '.intval($item['nr_ordem']).')">[remover]</a>'
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    if(trim($regulamento_alteracao['dt_alteracao_finalizada']) != '')
    {
        $grid->col_oculta = array(2);
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
        echo form_open('planos/regulamento_alteracao/salvar_glossario');
            echo form_start_box('cadastro', 'cadastro');
                echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']); 
                echo form_default_hidden('cd_regulamento_alteracao_glossario', '', $row['cd_regulamento_alteracao_glossario']); 
                echo form_default_hidden('fl_renumeracao', '', 'N');  
                if(intval($row['cd_regulamento_alteracao_glossario']) > 0)
                {
                    echo form_default_hidden('nr_ordem', '', $row);
                    echo form_default_row('', 'Ordem: ', $row['nr_ordem']);
                }
                else
                {
                    echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
                }
                echo form_default_textarea('ds_regulamento_alteracao_glossario', 'Descrição: (*)', $row, 'style="height:150px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar');
                if(intval($row['cd_regulamento_alteracao_glossario']) > 0)
                {	
                    echo button_save('Cancelar', 'cancelar();', 'botao_disabled');
                } 
            echo form_command_bar_detail_end();
        echo form_close();
        }
        echo br();
        echo $grid->render();
        echo br();
    echo aba_end();

    $this->load->view('footer');

?>