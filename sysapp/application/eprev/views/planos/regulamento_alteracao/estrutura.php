<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'cd_regulamento_alteracao_estrutura_tipo',
		'nr_ordem',
		'ds_regulamento_alteracao_estrutura'
	), 'verifica_ordem()') ?>

	function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/cadastro/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
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

    function set_estrutura_pai(cd_regulamento_alteracao_estrutura_pai)
    {
    	$.post("<?= site_url('planos/regulamento_alteracao/set_estrutura_pai') ?>",
		{
			cd_regulamento_alteracao               : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
			cd_regulamento_alteracao_estrutura_pai : cd_regulamento_alteracao_estrutura_pai
		},
		function(data)
		{
			$("#nr_ordem").val(data.nr_ordem);
			$("#cd_regulamento_alteracao_estrutura_tipo").val(data.cd_regulamento_alteracao_estrutura_tipo);

			$("#ds_regulamento_alteracao_estrutura").focus();
		}, 'json');	
    }

    function cancelar()
    {
    	location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function verifica_ordem()
    {
    	if($("#cd_regulamento_alteracao_estrutura").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_estrutura') ?>",
			{
				cd_regulamento_alteracao                : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
				cd_regulamento_alteracao_estrutura_pai  : $("#cd_regulamento_alteracao_estrutura_pai").val(),
				cd_regulamento_alteracao_estrutura_tipo : $("#cd_regulamento_alteracao_estrutura_tipo").val(),
				nr_ordem                                : $("#nr_ordem").val(),
                fl_verifica                             : 'R'
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

    function remover_estrutura(cd_regulamento_alteracao_estrutura, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_estrutura') ?>",
        {
            cd_regulamento_alteracao           : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_estrutura : cd_regulamento_alteracao_estrutura,
            cd_regulamento_alteracao_estrutura_tipo : $("#cd_regulamento_alteracao_estrutura_tipo").val(),
            nr_ordem                           : nr_ordem,
            fl_verifica                        : 'E'
        },
        function(data)
        {
            var confirmacao = 'Deseja remover a estrutura?\n\n'+
                        'Clique [Ok] para Sim\n\n'+
                        'Clique [Cancelar] para Não\n\n';

            if(data.fl_ordem == "S")
            {
                var confirmacao = 'Deseja remover a estrutura e reordenar os outros itens?\n\n'+
                        'Clique [Ok] para Sim\n\n'+
                        'Clique [Cancelar] para Não\n\n';
            }

            if(confirm(confirmacao))
            {
                location.href = "<?= site_url('planos/regulamento_alteracao/remover_estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_estrutura;
            }
            else
            {
                return false;
            }

        }, 'json');     
    }

    function excluir_estrutura(cd_regulamento_alteracao_estrutura, nr_ordem)
    {
        $.post("<?= site_url('planos/regulamento_alteracao/verifica_ordem_estrutura') ?>",
        {
            cd_regulamento_alteracao           : <?= $regulamento_alteracao['cd_regulamento_alteracao'] ?>,
            cd_regulamento_alteracao_estrutura : cd_regulamento_alteracao_estrutura,
            cd_regulamento_alteracao_estrutura_tipo : $("#cd_regulamento_alteracao_estrutura_tipo").val(),
            nr_ordem                           : nr_ordem,
            fl_verifica                        : 'E'
        },
        function(data)
        {
            var confirmacao = 'Deseja excluir a estrutura?\n\n'+
                        'Clique [Ok] para Sim\n\n'+
                        'Clique [Cancelar] para Não\n\n';

            if(data.fl_ordem == "S")
            {
                var confirmacao = 'Deseja excluir a estrutura e reordenar os outros itens?\n\n'+
                        'Clique [Ok] para Sim\n\n'+
                        'Clique [Cancelar] para Não\n\n';
            }

            if(confirm(confirmacao))
            {
                location.href = "<?= site_url('planos/regulamento_alteracao/excluir_estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>/"+cd_regulamento_alteracao_estrutura;
            }
            else
            {
                return false;
            }

        }, 'json');     
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
	$abas[] = array('aba_estrutura', 'Estrutura', TRUE, 'location.reload();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', FALSE, 'ir_versao();');

	$head = array(
		'Ordem',
		'Estrutura',
		'Descrição',
        'Qt. Artigos',
        ''
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array($item['ds_ordem'], 'text-align:right;'),
			array('<span class="'.$item['ds_class_label'].'">'.$item['ds_tipo'].'</span>', 'text-align:left;'),
			array('<span class="'.$item['ds_class_label'].'">'.$item['ds_regulamento_alteracao_estrutura'].'</span>', 'text-align:left;'),
            '<span class="badge badge-warning">'.$item['qt_artigo'].'</span>',
            (trim($regulamento_alteracao['dt_alteracao_finalizada']) == '' ?             
            anchor('planos/regulamento_alteracao/estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao'].'/'.$item['cd_regulamento_alteracao_estrutura'], '[editar]').' '.
            '<a href="javascript:void(0)" 
            onclick="remover_estrutura('.intval($item['cd_regulamento_alteracao_estrutura']).', '.intval($item['nr_ordem']).')">[remover]</a>'.' '.
            '<a href="javascript:void(0)" 
            onclick="excluir_estrutura('.intval($item['cd_regulamento_alteracao_estrutura']).', '.intval($item['nr_ordem']).')">[excluir]</a>'
            : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_box', 'Regulamento');
			echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
			echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');

        if(trim($regulamento_alteracao['dt_alteracao_finalizada']) == '')
        {
			echo form_open('planos/regulamento_alteracao/salvar_estrutura');
				echo form_start_box('default_estrutura_box', 'Cadastro');
					echo form_default_hidden('cd_regulamento_alteracao', '', $regulamento_alteracao['cd_regulamento_alteracao']); 
					echo form_default_hidden('fl_renumeracao', '', 'N'); 
					echo form_default_hidden('cd_regulamento_alteracao_estrutura', '', $row['cd_regulamento_alteracao_estrutura']);
					
					if(intval($row['cd_regulamento_alteracao_estrutura']) == 0)
					{
						echo form_default_dropdown('cd_regulamento_alteracao_estrutura_pai', 'Estrutura Pai:', $estrutura_pai, $row['cd_regulamento_alteracao_estrutura_pai'], 'onchange="set_estrutura_pai($(this).val())"');
						echo form_default_dropdown('cd_regulamento_alteracao_estrutura_tipo', 'Tipo: (*)', $estrutura_tipo, $row['cd_regulamento_alteracao_estrutura_tipo']);
						echo form_default_integer('nr_ordem', 'Ordem: (*)', $row);
					}	
					else
					{
						echo form_default_hidden('cd_regulamento_alteracao_estrutura_pai', '', $row['cd_regulamento_alteracao_estrutura_pai']);
						echo form_default_hidden('cd_regulamento_alteracao_estrutura_tipo', '', $row['cd_regulamento_alteracao_estrutura_tipo']);
						echo form_default_hidden('nr_ordem', '', $row['nr_ordem']);

						if(trim($row['ds_regulamento_alteracao_estrutura_pai']) != '')
						{
							echo form_default_row('', 'Estrutura Pai:', $row['ds_tipo'].' - '.$row['ds_regulamento_alteracao_estrutura_pai']);
						}
						
						echo form_default_row('', 'Tipo:', $row['ds_regulamento_alteracao_estrutura_tipo']);
						echo form_default_row('', 'Ordem:', $row['nr_ordem']);
					}
					
					echo form_default_text('ds_regulamento_alteracao_estrutura', 'Título: (*)', $row, 'style="width:450px;"');
				echo form_end_box('default_estrutura_box');
				echo form_command_bar_detail_start();
	                echo button_save('Salvar');  
	                if(intval($row['cd_regulamento_alteracao_estrutura']) > 0)
	                {	
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