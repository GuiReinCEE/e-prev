<?php
#echo "<PRE>".print_r($ar_cadastro,true)."</PRE>";exit;
#echo "<PRE>".print_r($ar_lista,true)."</PRE>";#exit;

	if(count($ar_cadastro) > 0)
	{
		echo form_start_box("identificacao_box", "<div style='text-align:left; width: 90%'>Identificação</div>",true);
			echo form_default_row('', 'Código:', $ar_cadastro['cd_link']);
			echo form_default_row('', 'Link Original:', $ar_cadastro['ds_link']);
			echo form_default_row('', 'Link Curto:', "http://fceee.com.br/?".$ar_cadastro['cd_link']);
			echo form_default_row('', 'Dt Inclusão:', $ar_cadastro['dt_inclusao']);
			echo form_default_row('', 'Dt Exclusão:', $ar_cadastro['dt_exclusao']);
		echo form_end_box("identificacao_box");
	
		#### DIA ####
		$body=array();
		$head = array( 
			'Dt Inicio',  
			'Dt Final',  
			'Qt Interno',
			'Qt Externo',
			'Total'
		);

		foreach($ar_lista as $ar_item)
		{
			$body[] = array(
					$ar_item['data_ini'],
					$ar_item['data_fim'],
					array($ar_item['qt_acesso_interno'],'text-align:center;','int'),
					array($ar_item['qt_acesso_externo'],'text-align:center;','int'),
					array(($ar_item['qt_acesso_interno'] + $ar_item['qt_acesso_externo']),'text-align:center;','int')
				);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->id_tabela  = 'tabela_linkLog';
		$grid->head       = $head;
		$grid->body       = $body;
		echo $grid->render();
		
		
	}
	else
	{
		echo "
			<br><br>
			<span style='color:red;' class='result_div_info'><b>Não foi encontrado nenhum registro.</b></span>
			<br><br>
			<span style='color:green;' class='result_div_info'><b>Verifique o(s) campo(s) e clique no botão [Filtrar] para exibir as informações</b></span>
		";
	}
	
	echo "<BR><BR><BR>";
?>