<?php
$body=array();
$head = array( 
	'Cód.',  
	'Descrição',
	'',
	''
);

$fl_escolha = $ar_entidade['fl_escolha'];
$fl_usuario = $ar_entidade['fl_usuario'];

echo '<div style="text-align:left; font-size: 130%;"><BR>';

if($ar_entidade['cd_entidade'] == 1)
{
	echo "Escolha um presente conforme as características dos idosos abaixo:<BR><BR>";
}

if($ar_entidade['cd_entidade'] == 2)
{
	echo "Doe uma peça de roupa ou um calçado conforme as características das crianças:<BR><BR>";
}

if($ar_entidade['cd_entidade'] == 3)
{
	echo "Escolha produtos que a casa necessita:<BR><BR>";
}

echo "</div>";

foreach($ar_entidade_item as $item)
{
	$ar_user = Array();
	
	if(count($ar_item_usuario[$item["cd_entidade_item"]]) == 0)
	{
		$lista_usuario = "Sem ajuda<BR><BR>";
	}
	else
	{
		$lista_usuario = "Total de ajudante(s): ".count($ar_item_usuario[$item["cd_entidade_item"]])."<BR><BR>";
	}

	foreach($ar_item_usuario[$item["cd_entidade_item"]] as $item_usuario)
	{
		$ar_user[] = $item_usuario['cd_usuario'];
		
		if(gerencia_in(array('GRI')))
		{		
			if($item_usuario['cd_usuario'] == $this->session->userdata('codigo'))
			{
				$lista_usuario.= "<B>".$item_usuario['ds_usuario']."</B><BR>";
			}
			else
			{
				$lista_usuario.= $item_usuario['ds_usuario']."<BR>";
			}		
		}
		else
		{
			if($item_usuario['cd_usuario'] == $this->session->userdata('codigo'))
			{
				$lista_usuario.= "<B>".$item_usuario['ds_usuario']."</B>";
			}		
		}
	}	

	
		$botao = (

			$fl_escolha == "N" ? 
				(count($ar_item_usuario[$item["cd_entidade_item"]]) == 0 ? 
					'<input type="button" value="Vou ajudar" onclick="incluiItemUsuario('.$item["cd_entidade_item"].')" class="botao">'  
					: 
					(in_array($this->session->userdata('codigo'),$ar_user) ? '<input type="button" value="Cancelar" onclick="excluirItemUsuario('.$item["cd_entidade_item_usuario"].')" class="botao_vermelho">' : ''))
				:
				(count($ar_item_usuario[$item["cd_entidade_item"]]) == 0 ? 
					'<input type="button" value="Vou ajudar" onclick="incluiItemUsuario('.$item["cd_entidade_item"].')" class="botao">'  
					: 
					(in_array($this->session->userdata('codigo'),$ar_user) ? '<input type="button" value="Cancelar" onclick="excluirItemUsuario('.$item["cd_entidade_item_usuario"].')" class="botao_vermelho">' : '<input type="button" value="Vou ajudar" onclick="incluiItemUsuario('.$item["cd_entidade_item"].')" class="botao">')
				)
		);
	
	
	$body[] = array(
		$item["cd_entidade_item"],
	    array($item["descricao"],'text-align:left;'),
		array($lista_usuario,'text-align:left;'),
		$botao
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>