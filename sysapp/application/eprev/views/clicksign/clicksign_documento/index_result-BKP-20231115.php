<?php
	#echo "<PRE>"; print_r($collection); exit;
	
	$FL_PROTOCOLO_INTERNO = (gerencia_in(array('GTI'))) ? TRUE : FALSE;
	
	#$FL_PROTOCOLO_INTERNO = false;
	
	if($FL_PROTOCOLO_INTERNO)
	{
		echo form_start_box("botoes_box", "Opções",true);
			echo form_default_row('', '', '
				<input type="button" onclick="protocolo();" value="Novo Protocolo Interno" class="btn btn-mini btn-info" style="width: 200px;"> 
			');
		echo form_end_box("botoes_box");
	}	

	$head[] = '<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">';
	$head[] = 'Dt. Inclusão';
	$head[] = 'Dt. Atualização';
	$head[] = 'Área';
	$head[] = 'Monitorado Área';
	$head[] = 'Usuário';
	$head[] = 'Situação';
	$head[] = 'Protocolo';
	$head[] = '#';

	if($FL_PROTOCOLO_INTERNO)
	{	
		$head[] = 'Cod Doc';
		$head[] = 'Pessoa';
	}
	
	$head[] = 'Descrição';
	




	$body = array();
	$_js_preenche = "";
	
	foreach ($collection as $key => $item) 
	{
		#$url_acoes = anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/".$item["id_doc"], "[consultar]", array('target' => "_blank"));
		#if (in_array($this->session->userdata('divisao'), array('GTI','GCM')) AND ($item['fl_status'] == 'RUNNING')) 
		#if(in_array($this->session->userdata('divisao'), array('GTI','GCM')))
		#{
		#	$url_acoes = br().anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/signatarioEditar/".md5($this->session->userdata('usuario').date("Ymd"))."/".$item["id_doc"], "[editar]", array('target' => "_blank"));
		#}
		
		$fl_check_recusa = FALSE;
		if ($item['fl_status'] == 'RUNNING')
		{
			$fl_check_recusa = TRUE;
		}
		
		
		$id = str_replace("-","",$item['id_doc'])."_";
		$id_part = array($id.'cd_empresa',$id.'cd_registro_empregado',$id.'seq_dependencia', $id.'nome_participante');
		$id_cod_doc = $id.'id_codigo';
		
		$campo_check = array(
			'name'        => $id.'chk',
			'id'          => $id.'chk',
			'value'       => $item['id_doc'],
			'checked'     => FALSE
		);	

		
		$_RE[$id.'cd_empresa']            = $item['cd_empresa']; 
		$_RE[$id.'cd_registro_empregado'] = $item['cd_registro_empregado']; 
		$_RE[$id.'seq_dependencia']       = $item['seq_dependencia']; 
		$_cd_documento                    = $item['cd_tipo_documento'];
		
		$campo_doc = array(
			'id_codigo'  => $id_cod_doc,
			'id_nome'    => $id.'nome_documento',
			'formulario' => FALSE,
			'value'      => $_cd_documento 
		);		
		
		if(intval($_cd_documento) > 0)
		{
			$_js_preenche.= "
								consultar_tipo_documentos__".$id_cod_doc."();
							";
		}		
		
		if(intval($_RE[$id.'cd_registro_empregado']) > 0)
		{
			$_js_preenche.= "
								consultar_participante__".$id."cd_empresa();
							";
		}		
		
		$ar_linha = array();
		$ar_linha[] = (($item['fl_status'] == 'CLOSED') ? form_checkbox($campo_check) : "");
		$ar_linha[] = $item['dt_inclusao'];
		$ar_linha[] = $item['dt_alteracao'];
		$ar_linha[] = $item['cd_area'];
		$ar_linha[] = '<span class="'.$item["cor_area_monitorar"].'">'.$item["ds_area_monitorar"].'</span>';
		$ar_linha[] = array( $item['nome'], 'text-align:left;');
		$ar_linha[] = '<span class="'.$item["cor_status"].'">'.$item["ds_status"].'</span>'.($fl_check_recusa == TRUE ? "<br><span id='obDocRecusado_".$item['id_doc']."'>verificando...</span> <script>getRecusa('".$item['id_doc']."')</script>" : "");
		$ar_linha[] = $item['id_doc'];
		$ar_linha[] = anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/signatarioEditar/".md5($this->session->userdata('usuario').date("Ymd"))."/".$item["id_doc"], (($item['fl_status'] == 'RUNNING') ? "[editar]" : "[consultar]"), array('target' => "_blank"));

		if($FL_PROTOCOLO_INTERNO)
		{
			$ar_linha[] = form_default_tipo_documento($campo_doc);
			$ar_linha[] = form_default_participante(
									$id_part,
									'',
									$_RE,
									true,
									true,
									'',
									false
									)
					.br(1)
					.form_input(array('name' => $id.'nome_participante', 'id' => $id.'nome_participante'), '', 'style="width:300px;"');			
		}

		$ar_linha[] = array(utf8_decode($item['documento']), 'text-align:left;');
		
		$body[] = $ar_linha;

	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->id_tabela  = 'tabela_digitalizado';
	$grid->head = $head;
	$grid->body = $body;

	echo $grid->render();
	
	echo br(3);
	
	if(trim($_js_preenche) != "")
	{
		echo "
				<script>
					".$_js_preenche."
				</script>
			 ";
	}	
?>