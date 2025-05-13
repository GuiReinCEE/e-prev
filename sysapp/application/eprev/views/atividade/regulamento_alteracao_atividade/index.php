<?php
	set_title('Regulamento de Plano - Atividade');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_regulamento_alteracao_atividade', 'cd_regulamento_alteracao_atividade_gerencia', 'cd_regulamento_alteracao_atividade_tipo'), 'salvar($(this))') ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/regulamento_alteracao_atividade/minhas') ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('atividade/regulamento_alteracao_atividade/acompanhamento/'.$row['cd_regulamento_alteracao_atividade']) ?>";
	}

	function set_pertinencia()
	{
		if($("#cd_regulamento_alteracao_atividade_tipo").val() == 1)
		{
			$("#dt_prevista_row").show();
			$("#dt_implementacao_row").show();
		}
		else
		{
			$("#dt_prevista_row").hide();
			$("#dt_prevista").val("");
			$("#dt_implementacao_row").hide();
			$("#dt_implementacao").val("");
		}
	}

	function salvar(form)
	{
		if($("#cd_regulamento_alteracao_atividade_tipo").val() == 1 && $("#dt_prevista").val() == '')
		{
			alert('Informe a Data Prevista.');
			$("#dt_prevista").focus();
		}
		else if($("#cd_regulamento_alteracao_atividade_tipo").val() == 0)
		{
			alert('Selecione a Pertinência.');
			$("#cd_regulamento_alteracao_atividade_tipo").focus()
		}
		else if($("#fl_dt_prevista").val() == 'S' && $("#dt_implementacao").val() == '')
		{
			alert('Informe a Data de Implementeção.');
			$("#dt_implementacao").focus()
		}
		else
		{
			var text = "Salvar?";

			if(confirm(text))
			{
				form.submit();
			}
		}
	}

	function set_implementacao()
	{
		if($("#dt_prevista").val() != '')
		{
			$("#label_dt_implementacao").text("Dt. Implementação: (*)");			
		}
	}

	$(function (){
		set_pertinencia();
		set_implementacao();
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
    $abas[] = array('aba_atividade', 'Atividade', TRUE, 'location.reload();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');

    echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $unidade_basica['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $unidade_basica['ds_cnpb']);
        echo form_end_box('default_box');
        echo form_start_box('default_unidade_basica_box', 'Unidade Básica');
        	echo form_default_row('', 'Estrutura:', '<span class="'.$unidade_basica['ds_class_label'].'">'.$unidade_basica['ds_estrutura'].'</span>');
            echo form_default_row('artigo', 'Artigo:', nl2br($unidade_basica['ds_artigo']));
            if(intval($row['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
            {
				echo form_default_row('ds_regulamento_alteracao_unidade_basica_pai', 'Unidade Pai:', $unidade_basica['ds_regulamento_alteracao_unidade_basica']);
            }
            echo form_default_row('ds_regulamento_alteracao_unidade_basica', 'Descrição:', nl2br($row['ds_regulamento_alteracao_unidade_basica']));
        echo form_end_box('default_unidade_basica_box');
	    echo form_open('atividade/regulamento_alteracao_atividade/salvar');
	    	echo form_start_box('default_cadastro_box', 'Cadastro');
	    		echo form_default_hidden('cd_regulamento_alteracao_atividade', '', $row['cd_regulamento_alteracao_atividade']);
	    		echo form_default_hidden('cd_regulamento_alteracao_atividade_gerencia', '', $row['cd_regulamento_alteracao_atividade_gerencia']);

	    		if(intval($row['cd_regulamento_alteracao_atividade_tipo']) == 0)
	    		{
	    			echo form_default_hidden('fl_pertinencia', '', 'N');
	    			echo form_default_dropdown('cd_regulamento_alteracao_atividade_tipo', 'Pertinência: (*)', $atividade_tipo, $row['cd_regulamento_alteracao_atividade_tipo'], 'onchange="set_pertinencia();"');
	    		}
	    		else
	    		{
	    			echo form_default_hidden('fl_pertinencia', '', 'S');
	    			echo form_default_hidden('cd_regulamento_alteracao_atividade_tipo', '', $row['cd_regulamento_alteracao_atividade_tipo']);
	    			echo form_default_row('', 'Pertinência:', '<span class="'.$row['ds_class_tipo'].'">'.$row['ds_regulamento_alteracao_atividade_tipo'].'<span>');
	    		}

	    		if(trim($row['dt_prevista']) == '')
	    		{
	    			echo form_default_hidden('fl_dt_prevista', '', 'N');
	    			echo form_default_date('dt_prevista', 'Dt. Prevista: (*)', $row['dt_prevista']);
	    		}
	    		else
	    		{
	    			echo form_default_hidden('fl_dt_prevista', '', 'S');
	    			echo form_default_hidden('dt_prevista', '', $row['dt_prevista']);
	    			echo form_default_row('', 'Dt. Prevista:', $row['dt_prevista']);
	    		}

	    		if(trim($row['dt_implementacao']) == '')
	    		{
	    			echo form_default_date('dt_implementacao', '<span id="label_dt_implementacao">Dt. Implementação:</span>', $row['dt_implementacao']);
	    		}
	    		else
	    		{
	    			echo form_default_row('', 'Dt. Implementação:', $row['dt_implementacao']);
	    		}
	        echo form_end_box('default_cadastro_box');
	        if(trim($row['dt_implementacao']) == '')
	        {
				echo form_command_bar_detail_start();
					echo button_save('Salvar');
				echo form_command_bar_detail_end();
	        }
		echo form_close(); 
    echo aba_end();
    echo br();
    $this->load->view('footer');
?>