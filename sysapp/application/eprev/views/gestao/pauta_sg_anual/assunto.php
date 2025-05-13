<?php
	set_title('Pauta SG Anual');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('mes', 'cd_gerencia_responsavel', 'ds_assunto', 'cd_pauta_sg_objetivo', 'cd_pauta_sg_justificativa', 'nr_tempo')) ?>

	function ir_lista()
    {
        location.href = "<?= site_url('gestao/pauta_sg_anual/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/pauta_sg_anual/cadastro/'.$row['cd_pauta_sg_anual']) ?>";
    }

    function cancelar()
    {
        location.href = "<?= site_url('gestao/pauta_sg_anual/assunto/'.$row['cd_pauta_sg_anual']) ?>";
    }

    function excluir_assunto(cd_pauta_sg_anual_assunto)
    {
        var confirmacao = 'Deseja excluir o assunto?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
            location.href = "<?= site_url('gestao/pauta_sg_anual/excluir_assunto/'.$row['cd_pauta_sg_anual']) ?>/"+cd_pauta_sg_anual_assunto+"/S";
        }  
    }
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_assunto', 'Assuntos', TRUE, 'location.reload();');

	$this->load->helper('grid');
	$grid = new grid();
	$grid_gerencia = new grid();
	

	$head = array(
		'Gerência',
		'Dt. Encerramento',
		'Usuário'
	);

	$body = array();

	foreach ($pauta_gerencia as $item)
	{
	  	$body[] = array(
		    $item['cd_gerencia'],
		    $item['dt_encerramento'],
		    array($item['ds_usuario_encerramento'], 'text-align:left')
		);
	}

	$grid_gerencia->view_count = false;
	$grid_gerencia->head = $head;
	$grid_gerencia->body = $body;
	$grid_gerencia->id_tabela = "table-gerencia";

	$head = array(
		'Mês de Aprovação',
		'Ger. Responsável',
		'Assunto',
		'Objetivo',
		'Justificativa',
		'Tempo',
		'Dt. Inclusão',
		'Usuário',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{
	  	$body[] = array(
			array(anchor('gestao/pauta_sg_anual/assunto/'.$row['cd_pauta_sg_anual'].'/'.$item['cd_pauta_sg_anual_assunto'], mes_extenso($item['mes'])), 'text-align:left'),
		    $item['cd_gerencia_responsavel'],
		    array(anchor('gestao/pauta_sg_anual/assunto/'.$row['cd_pauta_sg_anual'].'/'.$item['cd_pauta_sg_anual_assunto'], nl2br($item['ds_assunto'])), 'text-align:justify'),
		    $item['ds_pauta_sg_objetivo'],
		    $item['ds_pauta_sg_justificativa'],
		    $item['nr_tempo'],
		    $item['dt_inclusao'],
		    $item['ds_usuario_inclusao'],
		    (trim($pauta_sg_anual['dt_confirmacao']) == '' ? '<a href="javascript:void(0)" onclick="excluir_assunto('.intval($item['cd_pauta_sg_anual_assunto']).');">[excluir]</a>' : '')
		);
	}

	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/pauta_sg_anual/salvar_assunto');
			echo form_start_box('default_box', 'Pauta');
                echo form_default_hidden('cd_pauta_sg_anual', '', $row['cd_pauta_sg_anual']); 
                echo form_default_hidden('cd_pauta_sg_anual_assunto', '', $row['cd_pauta_sg_anual_assunto']); 
                echo form_default_hidden('nr_ano', '', $pauta_sg_anual['nr_ano']); 
                echo form_default_row('ds_colegiado', 'Colegiado:', '<span class="'.$pauta_sg_anual['ds_class_colegiado'].'">'.$pauta_sg_anual['ds_colegiado'].'</span>');
                echo form_default_row('', 'Ano:', '<span class="label label-inverse">'.$pauta_sg_anual['nr_ano'].'</span>');
                if(trim($pauta_sg_anual['dt_envio_responsavel']) != '')
                {
                	echo form_default_row('dt_envio_responsavel', 'Dt. Envio Responsável:', $pauta_sg_anual['dt_envio_responsavel']);
                	echo form_default_row('ds_usuario_envio_resposanvel', 'Usuário Envio:', $pauta_sg_anual['ds_usuario_envio_resposanvel']);
                }
                echo form_default_row('dt_limite', 'Dt. Limite:', $pauta_sg_anual['dt_limite']);

                if(trim($pauta_sg_anual['dt_confirmacao']) != '')
                {
                	echo form_default_row('dt_confirmacao', 'Dt. Confirmação do Colegiado:', $pauta_sg_anual['dt_confirmacao']);
                }
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
			echo form_command_bar_detail_end();
			echo $grid_gerencia->render(); 
			echo form_start_box('default_assunto_box', 'Assunto');
                echo form_default_dropdown('mes', 'Mês de Aprovação: (*)', $mes, $row['mes']);
                echo form_default_dropdown('cd_gerencia_responsavel', 'Gerência Responsável: (*)', $gerencia, $row['cd_gerencia_responsavel']);
                echo form_default_textarea('ds_assunto', 'Assunto: (*)', $row['ds_assunto'], 'style="height:150px;"');
               	echo form_default_dropdown('cd_pauta_sg_objetivo', 'Objetivo: (*)', $objetivo, $row['cd_pauta_sg_objetivo']);
            	echo form_default_dropdown('cd_pauta_sg_justificativa', 'Justificativa: (*)', $justificativa, $row['cd_pauta_sg_justificativa']);
            	echo form_default_integer('nr_tempo', 'Tempo (min): (*)', $row);
			echo form_end_box('default_assunto_box');
			echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
                if(intval($row['cd_pauta_sg_anual_assunto']) > 0)
                {
                	echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
                }
		    echo form_command_bar_detail_end();
		echo form_close();
		echo $grid->render();        
		echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');
?>