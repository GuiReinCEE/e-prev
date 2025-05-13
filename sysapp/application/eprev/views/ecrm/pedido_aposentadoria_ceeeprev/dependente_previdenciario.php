<?php
	set_title('Pedido de Aposentadoria CeeePrev');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_nome')); ?>

	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev') ?>";
    }

    function ir_cadastro()
    {
    	location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cadastro['cd_pedido_aposentadoria_ceeeprev']) ?>";
    }

    function ir_dependente()
    {
    	location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/dependente/'.$cadastro['cd_pedido_aposentadoria_ceeeprev']) ?>";
    }

    function excluir(cd_pedido_aposentadoria_ceeeprev_dependente_prev)
	{
		var confirmacao = 
		 	'Deseja excluir o dependente?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        { 
			location.href = "<?= site_url('ecrm/pedido_aposentadoria_ceeeprev/excluir_dependente_previdenciario/'.intval($cadastro['cd_pedido_aposentadoria_ceeeprev'])) ?>/"+cd_pedido_aposentadoria_ceeeprev_dependente_prev;
		}
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_dependente', 'Dependentes IR', FALSE, 'ir_dependente();');
	$abas[] = array('aba_dependente_prev', 'Dependentes Previdenciário', TRUE, 'location.reload();');

	$drop = array(
		array('value' => 'N', 'text' => 'NÃO'),
		array('value' => 'S', 'text' => 'SIM'),
	);

	$sexo = array(
		array('value' => 'M', 'text' => 'Masculino'),
		array('value' => 'F', 'text' => 'Feminino'),
	);

	$head = array( 
		'Nome',
		'Data de Nascimento',
		'Sexo',
		'Grau de Parentesco',
		'Estado Cívil',
		'Incapaz',
		'Opção',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
		$body[] = array(
			(trim($item['fl_opcao']) == 'I' ?  anchor('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario/'.$item['cd_pedido_aposentadoria_ceeeprev'].'/'.$item['cd_pedido_aposentadoria_ceeeprev_dependente_prev'], $item['ds_nome']) : $item['ds_nome']),
			(trim($item['fl_opcao']) == 'I' ?  anchor('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario/'.$item['cd_pedido_aposentadoria_ceeeprev'].'/'.$item['cd_pedido_aposentadoria_ceeeprev_dependente_prev'], $item['dt_nascimento']) : $item['dt_nascimento']),
			$item['ds_sexo'],
			$item['ds_grau_parentesco'],
			$item['ds_estado_civil'],
			$item['fl_incapaz'],
			$item['ds_opcao'],
			(trim($item['fl_opcao']) == 'I' ?  '<a href="javascript:void(0)" onclick="excluir('.$item['cd_pedido_aposentadoria_ceeeprev_dependente_prev'].')">[excluir]</a>' : '')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('ecrm/pedido_aposentadoria_ceeeprev/salvar_dependente_previdenciario');
			echo form_start_box('default_box_cadastro', 'Dados Cadastrais');
				echo form_default_hidden('cd_pedido_aposentadoria_ceeeprev', '', $cadastro['cd_pedido_aposentadoria_ceeeprev']);
				echo form_default_row('re', 'RE:', '<span class="label label-inverse">'.$cadastro['cd_empresa'].'/'.$cadastro['cd_registro_empregado'].'/'.$cadastro['seq_dependencia'].'</span>');
				echo form_default_row('ds_pedido_aposentadoria', 'Pedido de Aposentadoria:', '<span class="label label-inverse">'.$cadastro['ds_pedido_aposentadoria'].'</span>');
				echo form_default_row('', 'Status:', '<span class="'.$cadastro['ds_class_status'].'">'.$cadastro['ds_status'].'</span>');
				echo form_default_row('ds_nome', 'Nome:', $cadastro['ds_nome']);
			echo form_end_box('default_box_cadastro');

			echo form_start_box('default_box_dependente', 'Dependente');
				echo form_default_hidden('cd_pedido_aposentadoria_ceeeprev_dependente_prev', '', $row['cd_pedido_aposentadoria_ceeeprev_dependente_prev']);
				echo form_default_text('ds_nome', 'Nome:', $row['ds_nome'], 'style="width:350px;"');
				echo form_default_date('dt_nascimento', 'Data de Nascimento:', $row['dt_nascimento']);
				echo form_default_dropdown('ds_sexo', 'Sexo:', $sexo, $row['ds_sexo']);
				echo form_default_dropdown('ds_grau_parentesco', 'Grau de Parentesco:', $grau_parentesco, $row['ds_grau_parentesco']);
				echo form_default_dropdown('ds_estado_civil', 'Estado Cívil:', $estado_civil, $row['ds_estado_civil']);
				echo form_default_dropdown('fl_incapaz', 'Incapaz:', $drop, $row['fl_incapaz']);
			echo form_end_box('default_box_dependente');

			echo form_command_bar_detail_start();
				echo button_save('Salvar');
            echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(10);
	echo aba_end();

	$this->load->view('footer');
?>