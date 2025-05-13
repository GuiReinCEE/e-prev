<?php
	set_title('Regulamento Revisão');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(
            'nr_ordem', 
            'ds_regulamento_revisao', 
        ), 'verifica_ordem()'); 
     ?>

    function cancelar()
    {
        location.href = "<?= site_url('planos/regulamento_revisao/index'); ?>"
    }

    function set_pai(cd_regulamento_revisao_pai)
    {
    	$.post("<?= site_url('planos/regulamento_revisao/set_pai') ?>",
		{
			cd_regulamento_revisao_pai : cd_regulamento_revisao_pai
		},
		function(data)
		{
			$("#nr_ordem").val(data.nr_ordem);

			$("#ds_regulamento_revisao").focus();
		}, 'json');	
    }

    function verifica_ordem()
    { 
    	if($("#cd_regulamento_revisao").val() == 0)
    	{
    		$.post("<?= site_url('planos/regulamento_revisao/verifica_ordem') ?>",
			{
				cd_regulamento_revisao_pai : $("#cd_regulamento_revisao_pai").val(),
				nr_ordem                   : $("#nr_ordem").val()
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

    function remover(cd_regulamento_revisao, nr_ordem, cd_regulamento_revisao_pai)
    {
    	var confirmacao = 'Deseja remover e reordenar os outros itens?\n\n'+
                          'Clique [Ok] para Sim\n\n'+
                          'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('planos/regulamento_revisao/remover/') ?>/"+cd_regulamento_revisao+"/"+nr_ordem+"/"+cd_regulamento_revisao_pai;
		}
    }
</script>
<?php
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload()');

    $head = array( 
        'Ordem',
        'Descrição',
        'Observações',
        'Não se aplica',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            array($item['ds_ordem'], 'text-align:right;'),
            array(nl2br(anchor('planos/regulamento_revisao/index/'.$item['cd_regulamento_revisao'], $item['ds_regulamento_revisao'])), 'text-align:justify;'),
            array(nl2br(anchor('planos/regulamento_revisao/index/'.$item['cd_regulamento_revisao'], $item['ds_descricao'])), 'text-align:justify;'),
            array(implode(br(), $item['regulamento_alteracao_revisao_tipo']), 'text-align:left'),
            '<a href="javascript:void(0)"
            onclick="remover('.intval($item['cd_regulamento_revisao']).', '.intval($item['nr_ordem']).', '.intval($item['cd_regulamento_revisao_pai']).')">[remover]</a>'
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

    echo aba_start($abas);
        echo form_open('planos/regulamento_revisao/salvar');
            echo form_start_box('cadastro_box', 'Cadastro');
                echo form_default_hidden('cd_regulamento_revisao', '', $row); 
                echo form_default_hidden('fl_renumeracao', '', 'N'); 	
                echo form_default_dropdown('cd_regulamento_revisao_pai', 'Regulamento Revisão Pai:', $regulamento_revisao_pai, $row['cd_regulamento_revisao_pai'], 'onchange="set_pai($(this).val())"');
                echo form_default_integer('nr_ordem', 'Ordem: (*)', $row); 
                echo form_default_textarea('ds_regulamento_revisao', 'Descrição: (*)', $row, 'style="height:100px;"'); 	
                echo form_default_textarea('ds_descricao', 'Observações:', $row, 'style="height:100px;"'); 
                echo form_default_checkbox_group('cd_regulamento_tipo', 'Não se aplica:', $regulamento_revisao_tipo, $regulamento_alteracao_revisao_tipo);	
            echo form_end_box('cadastro_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar');	
                if(intval($row['cd_regulamento_revisao']) != 0)
                {
                    echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna')
?>