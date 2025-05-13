<?php
	$head = array( 
		'Cargo',

		'Nome',
		'CPF',
		'Vinculação',
		'Status',	
		'Código',		
		'Telefones',
		'E-Mail',
		'Dt. Inclusão',
		'Dt. Cancelamento',
		'Dt. Aprovação',
		'Usuário Aprovação'	
	);

	$body = array();

	foreach($collection as $item)
	{

		$telefone = array();
		
		if(trim($item['ds_telefone_1']) != '')
		{
			$telefone[] = str_replace(' ', '', $item['ds_telefone_1']);
		}

		if(trim($item['ds_telefone_2']) != '')
		{
			$telefone[] = str_replace(' ', '', $item['ds_telefone_2']);
		}

		$email = array();
		
		if(trim($item['ds_email_1']) != '')
		{
			$email[] = str_replace(' ', '', $item['ds_email_1']);
		}

		if(trim($item['ds_email_2']) != '')
		{
			$email[] = str_replace(' ', '', $item['ds_email_2']);
		}

		$body[] = array(
			'<span class="'.$item['class_cargo'].'">'.$item['tp_cargo'].'</span>',
			array($item['ds_nome'], 'text-align:left'),
			$item['ds_cpf'],
			array($item['ds_vinculacao'], 'text-align:left'),
			
			'<span class="'.$item['class_status'].'">'.$item['ds_status'].'</span>',
			array(anchor('gestao/formulario_inscricao_eleicao/cadastro/'.$item['cd_formulario_inscricao_eleicao'], $item['ds_codigo']), 'text-align:left;'),
			implode(br(), $telefone),
			implode(br(), $email),
			$item['dt_inclusao'],
			$item['dt_cancelamento'],
			$item['dt_aprovacao'],
			array($item['ds_usuario_aprovacao'], 'text-align:left')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
?>