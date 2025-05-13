<?php
#echo "<PRE>".print_r($ar_cadastro,true)."</PRE>";exit;
#echo "<PRE>".print_r($ar_lista,true)."</PRE>";#exit;

	if(count($ar_cadastro) > 0)
	{
		echo '
				<script> 
					$("#cd_link").val('.$ar_cadastro['cd_link'].');
					$("#aba_tecnologia").show();
				</script>
			 ';
		
		echo form_start_box("identificacao_box", "<div style='text-align:left; width: 90%'>Identificação</div>",true);
			echo form_default_row('', 'Código:', $ar_cadastro['cd_link']);
			echo form_default_row('', 'Link Original:', $ar_cadastro['ds_link']);
			echo form_default_row('', 'Link Curto:', "http://fceee.com.br/?".$ar_cadastro['cd_link']);
			echo form_default_row('', 'Dt Inclusão:', $ar_cadastro['dt_inclusao']);
			echo form_default_row('', 'Dt Exclusão:', $ar_cadastro['dt_exclusao']);
		echo form_end_box("identificacao_box");
	
		#### DIA ####
		echo form_start_box("tabela_linkLog_box", "<div style='text-align:left; width: 90%'>Por Dia</div>",true);
		$body=array();
		$head = array( 
			'Data',  
			'Qt Interno',
			'Qt Externo',
			'Total',
			''
		);

		foreach($ar_lista as $ar_item)
		{
			$body[] = array(
					$ar_item['data'],
					array($ar_item['qt_acesso_interno'],'text-align:center;','int'),
					array($ar_item['qt_acesso_externo'],'text-align:center;','int'),
					array(($ar_item['qt_acesso_interno'] + $ar_item['qt_acesso_externo']),'text-align:center;','int'),
					($tipo == "P" ? ('<a href="#" onclick="linkLogDia(\''.$ar_item['data'].'\')">[ Ver dia ]</a>') : "")
				);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->id_tabela  = 'tabela_linkLog';
		$grid->head       = $head;
		$grid->body       = $body;
		echo $grid->render();
		echo form_end_box("tabela_linkLog_box");
		
		
		echo form_start_box("tabela_linkLogHora_box", "<div style='text-align:left; width: 90%'>Por Horário</div>",true);
		#### HORA ####
		$body=array();
		$head = array( 
			'Horário',  
			'Qt Interno',
			'Qt Externo',
			'Total'
		);

		foreach($ar_lista_hora as $ar_item)
		{
			$body[] = array(
					$ar_item['hora_ini']." até ".$ar_item['hora_fim'],
					array($ar_item['qt_acesso_interno'],'text-align:center;','int'),
					array($ar_item['qt_acesso_externo'],'text-align:center;','int'),
					array(($ar_item['qt_acesso_interno'] + $ar_item['qt_acesso_externo']),'text-align:center;','int')
				);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->id_tabela  = 'tabela_linkLogHora';
		$grid->head       = $head;
		$grid->body       = $body;
		echo $grid->render();	
		echo form_end_box("tabela_linkLogHora_box");		
		
	}
	else
	{
		echo '
				<script> 
					$("#cd_link").val("");
					$("#aba_tecnologia").hide();
				</script>
			 ';	
	
		echo "
			<br><br>
			<span class='label label-important'>Não foi encontrado nenhum registro.</span>
			<br><br>
			<span class='label label-success'>Verifique o(s) campo(s) e clique no botão [Filtrar] para exibir as informações</span>
		";
	}
	
	echo br(5);
?>