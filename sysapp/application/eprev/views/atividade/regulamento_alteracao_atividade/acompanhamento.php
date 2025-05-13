<?php
	set_title('Regulamento de Plano - Atividade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_regulamento_alteracao_atividade', 'cd_regulamento_alteracao_atividade_gerencia', 'ds_regulamento_alteracao_atividade_acompanhamento')) ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/regulamento_alteracao_atividade/minhas') ?>";
	}

	function ir_atividade()
	{
		location.href = "<?= site_url('atividade/regulamento_alteracao_atividade/index/'.$atividade['cd_regulamento_alteracao_atividade']) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('atividade/regulamento_alteracao_atividade/acompanhamento/'.$atividade['cd_regulamento_alteracao_atividade']) ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
            "CaseInsensitiveString",
		    "DateTimeBR",
		    "CaseInsensitiveString"
		]);
		ob_resul.onsort = function ()
		{
			var atividades = ob_resul.tBody.atividades;
			var l = atividades.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(atividades[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(atividades[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(1, true);
	}

	$(function (){
		configure_result_table();
	});

</script>
<style>
    #ds_regulamento_alteracao_unidade_basica_item, #ds_regulamento_alteracao_unidade_basica_pai_item, #artigo_item
    {
        white-space:normal !important;
    }
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', TRUE, 'location.reload();');

    $head = array(
        'Descrição',
        'Dt. Inclusão',
        'Usuário'
    );

    $body = array();

    foreach ($collection as $key => $item)
    {
    	$link = $item['ds_regulamento_alteracao_atividade_acompanhamento'];

    	if(trim($atividade['dt_implementacao']) == '' AND intval($item['cd_usuario_inclusao']) == intval($cd_usuario))
    	{
    		$link = anchor('atividade/regulamento_alteracao_atividade/acompanhamento/'.$atividade['cd_regulamento_alteracao_atividade'].'/'.$item['cd_regulamento_alteracao_atividade_acompanhamento'], $item['ds_regulamento_alteracao_atividade_acompanhamento']);
    	}

    	$body[] = array(
    		array($link, 'text-align:justify'),
	    	$item['dt_inclusao'],
	    	$item['ds_usuario']
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $unidade_basica['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $unidade_basica['ds_cnpb']);
        echo form_end_box('default_box');
        echo form_start_box('default_unidade_basica_box', 'Unidade Básica');
        	echo form_default_row('', 'Estrutura:', '<span class="'.$unidade_basica['ds_class_label'].'">'.$unidade_basica['ds_estrutura'].'</span>');
            echo form_default_row('artigo', 'Artigo:', nl2br($unidade_basica['ds_artigo']));
            if(intval($atividade['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
            {
				echo form_default_row('ds_regulamento_alteracao_unidade_basica_pai', 'Unidade Pai:', $unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }
            echo form_default_row('ds_regulamento_alteracao_unidade_basica', 'Descrição:', nl2br($atividade['ds_regulamento_alteracao_unidade_basica']));
        echo form_end_box('default_unidade_basica_box');
    	if(trim($atividade['dt_implementacao']) == '')
        {
		    echo form_open('atividade/regulamento_alteracao_atividade/salvar_acompanhamento');
		    	echo form_start_box('default_cadastro_box', 'Cadastro');
		    		echo form_default_hidden('cd_regulamento_alteracao_atividade', '', $atividade['cd_regulamento_alteracao_atividade']);
		    		echo form_default_hidden('cd_regulamento_alteracao_atividade_gerencia', '', $atividade['cd_regulamento_alteracao_atividade_gerencia']);
		    		echo form_default_hidden('cd_regulamento_alteracao_atividade_acompanhamento', '', $row['cd_regulamento_alteracao_atividade_acompanhamento']);
		    		echo form_default_textarea('ds_regulamento_alteracao_atividade_acompanhamento', 'Descrição: (*)', $row);
		        echo form_end_box('default_cadastro_box');
					echo form_command_bar_detail_start();
						echo button_save('Salvar');
						if(intval($row['cd_regulamento_alteracao_atividade_acompanhamento']) > 0)
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
    $this->load->view('footer');
?>