<?php
$head = array(
	'RE',
	'Nome',
	'E-mail',
	'APP',
	'Dt. Removido',
	''
);

$body = array();

foreach ($collection as $item)
{	
	$body[] = array(
		(((trim($item['dt_exclusao']) == '') AND ($fl_email == 'S'))
			?  '
				<a href="javascript:void(0)" onclick="ver_email('.$item['cd_empresa'].','.$item['cd_registro_empregado'].','.$item['seq_dependencia'].')">
				'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'].'
			   	</a>' 
			: $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia']
		),
		array($item['nome'], 'text-align:left;'),
		'<span class="'.$item['ds_class_email'].'">'.$item['ds_email'].'</span>',
		'<span class="'.$item['ds_class_app'].'">'.$item['ds_app'].'</span>',
		$item['dt_exclusao'],

		

		(((trim($item['dt_exclusao']) == '') AND ($fl_email == 'S'))
			? '<a href="javascript:void(0)" onclick="ver_email('.$item['cd_empresa'].','.$item['cd_registro_empregado'].','.$item['seq_dependencia'].')">[ver]</a>  '.
			  (trim($row['dt_envio']) == '' ? '<a href="javascript:void(0)" onclick="enviar_email('.$item['cd_empresa'].','.$item['cd_registro_empregado'].','.$item['seq_dependencia'].')">[enviar teste]</a> ' : '')
			: ''
		) 

		.
		(trim($row['dt_envio']) == '' ?
			(trim($item['dt_exclusao']) == '' 
				? '<a href="javascript:void(0)" onclick="remover('.$item['cd_campanha_aumento_contrib_inst_participante'].')">[remover]</a>' 
			 	: '<a href="javascript:void(0)" onclick="adicionar('.$item['cd_campanha_aumento_contrib_inst_participante'].')">[adicionar]</a>'
			)
		: '')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo $grid->render();
?>