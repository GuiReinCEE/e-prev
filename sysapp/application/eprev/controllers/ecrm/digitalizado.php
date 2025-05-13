<?php
class digitalizado extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('projetos/Digitalizado_model');
    }
	
    function index()
    {
		CheckLogin();
		
		$args = Array();	
		$data = Array();	
		
		$args['divisao'] = $this->session->userdata('divisao');
		
		$this->Digitalizado_model->usuarioCombo($result, $args);
		$data['ar_usuario'] = $result->result_array();		

		$data['tipo_solicitacao'] = $this->Digitalizado_model->carregar_tipo_solicitacao();
		
		$this->load->view('ecrm/digitalizado/index.php',$data);
    }	

	function listarDocumentos($ar_param)
	{
		$ar_arq = Array();
		$args   = Array();
		$data   = Array();
		$result = null;
		
		$args['ar_tipo']      = array('pdf','jpg','png','txt');
		$args['dir']          = $ar_param['dir'];
		$fl_protocolo_interno = $ar_param['protocolo'];
		$filtro_usuario       = $ar_param['usuario'];
		
		$this->Digitalizado_model->listar($result, $args);
		$ar_arq = $result;
		
		$data['ar_lista'] = Array();
		if(count($ar_arq) > 0)
		{
			$nr_conta = 0;
			$nr_fim = count($ar_arq);
			$i = 0;
			while($nr_conta < $nr_fim)
			{
				$args   = Array();
				$result = null;			
				
				$ar_tmp = explode("_", $ar_arq[$nr_conta]['name']);
				$usuario = $ar_tmp[0];
				
				if((($usuario == $filtro_usuario) or (strtoupper($usuario) == $this->session->userdata('divisao'))) or ($filtro_usuario == ""))
				{
					$ar_arq[$nr_conta]['usuario'] = $usuario;
				
					$args['arquivo'] = md5($ar_arq[$nr_conta]['name']).".".$ar_arq[$nr_conta]['ext'];
					$this->Digitalizado_model->listaProtocoloInterno($result, $args);
					$ar_arq[$nr_conta]['ar_protocolo_interno'] = $result->result_array();
					
					if(($fl_protocolo_interno == "S") and (count($ar_arq[$nr_conta]['ar_protocolo_interno']) > 0))
					{
						$digitalizado = $this->Digitalizado_model->get_digitalizado($ar_arq[$nr_conta]['id_file']);

						$data['ar_lista'][$i] = $ar_arq[$nr_conta];

						$data['ar_lista'][$i]['cd_digitalizado']       = (isset($digitalizado['cd_digitalizado']) != '' ? intval($digitalizado['cd_digitalizado']) : 0);
						$data['ar_lista'][$i]['cd_documento']          = (isset($digitalizado['cd_documento']) != '' ? $digitalizado['cd_documento'] : '');
						$data['ar_lista'][$i]['cd_empresa']            = (isset($digitalizado['cd_empresa']) != '' ? $digitalizado['cd_empresa'] : '');
						$data['ar_lista'][$i]['cd_registro_empregado'] = (isset($digitalizado['cd_registro_empregado']) != '' ? $digitalizado['cd_registro_empregado'] : '');
						$data['ar_lista'][$i]['seq_dependencia']       = (isset($digitalizado['seq_dependencia']) != '' ? $digitalizado['seq_dependencia'] : '');

						$i++;
					}
					else if(($fl_protocolo_interno == "N") and (count($ar_arq[$nr_conta]['ar_protocolo_interno']) == 0)) 
					{
						$digitalizado = $this->Digitalizado_model->get_digitalizado($ar_arq[$nr_conta]['id_file']);

						$data['ar_lista'][$i] = $ar_arq[$nr_conta];

						$data['ar_lista'][$i]['cd_digitalizado']       = (isset($digitalizado['cd_digitalizado']) != '' ? intval($digitalizado['cd_digitalizado']) : 0);
						$data['ar_lista'][$i]['cd_documento']          = (isset($digitalizado['cd_documento']) != '' ? $digitalizado['cd_documento'] : '');
						$data['ar_lista'][$i]['cd_empresa']            = (isset($digitalizado['cd_empresa']) != '' ? $digitalizado['cd_empresa'] : '');
						$data['ar_lista'][$i]['cd_registro_empregado'] = (isset($digitalizado['cd_registro_empregado']) != '' ? $digitalizado['cd_registro_empregado'] : '');
						$data['ar_lista'][$i]['seq_dependencia']       = (isset($digitalizado['seq_dependencia']) != '' ? $digitalizado['seq_dependencia'] : '');

						$i++;
					}
					else if(($fl_protocolo_interno != "S") and ($fl_protocolo_interno != "N"))
					{
						$digitalizado = $this->Digitalizado_model->get_digitalizado($ar_arq[$nr_conta]['id_file']);

						$data['ar_lista'][$i] = $ar_arq[$nr_conta];

						$data['ar_lista'][$i]['cd_digitalizado']       = (isset($digitalizado['cd_digitalizado']) != '' ? intval($digitalizado['cd_digitalizado']) : 0);
						$data['ar_lista'][$i]['cd_documento']          = (isset($digitalizado['cd_documento']) != '' ? $digitalizado['cd_documento'] : '');
						$data['ar_lista'][$i]['cd_empresa']            = (isset($digitalizado['cd_empresa']) != '' ? $digitalizado['cd_empresa'] : '');
						$data['ar_lista'][$i]['cd_registro_empregado'] = (isset($digitalizado['cd_registro_empregado']) != '' ? $digitalizado['cd_registro_empregado'] : '');
						$data['ar_lista'][$i]['seq_dependencia']       = (isset($digitalizado['seq_dependencia']) != '' ? $digitalizado['seq_dependencia'] : '');

						$i++;
					}
				}
				
				
				$nr_conta++;
			}
		}

		#echo "<PRE>". print_r($ar_arq,true)."</PRE>";
		return $data['ar_lista'];
	}
	
    function listar()
    {
		CheckLogin();
		
		$args   = Array();
		$data   = Array();
		$result = null;		
		
		$args['dir']              = $this->session->userdata('divisao');
		$args['protocolo']        = $this->input->post("fl_protocolo_interno", TRUE);
		$args['tp_digitalizacao'] = $this->input->post("tp_digitalizacao", TRUE);
		$args['usuario']          = $this->input->post("ds_usuario", TRUE);
		
		manter_filtros($args);
		
		$data['tp_digitalizacao'] = $args['tp_digitalizacao'];
	
		$data['ar_lista'] = $this->listarDocumentos($args);
		$this->load->view('ecrm/digitalizado/index_result', $data);
    }	
	
    function listarJson($p_dir="", $p_usuario="", $p_protocolo="")
    {
		$args   = Array();
		$data   = Array();
		$result = null;		
		
		$args['dir']       = trim($p_dir);
		$args['usuario']   = trim($p_usuario);
		$args['protocolo'] = trim($p_protocolo);
	
		$ar_arq = $this->listarDocumentos($args);
	
		echo json_encode($ar_arq);
    }	
	
	function avisoGAP()
	{
		$args   = Array();
		$data   = Array();
		$result = null;		

		$args              = Array();
		$args['dir']       = "GCM";
		$args['protocolo'] = "N";			
		$args['usuario']   = "";
		
		$ar_doc = Array();
		$ar_doc = $this->listarDocumentos($args);		
		
		$ar_aviso = Array();
		foreach($ar_doc as $ar_item)
		{
			if(!in_array($ar_item["usuario"], $ar_aviso))
			{
				$ar_aviso[] = trim($ar_item['usuario']);
			}
		}
		
		if(count($ar_aviso))
		{
			foreach($ar_aviso as $item)
			{
				$ar_email['email'] = trim($item)."@eletroceee.com.br";
				$this->Digitalizado_model->avisoGAP($result, $ar_email);
			}			
		}
		
		$ar_retorno = array("STATUS" => "OK", "RETORNO" => "");
		echo json_encode($ar_retorno);			
	}
	
	function protocolo()
	{
		CheckLogin();
		$this->load->model('projetos/documento_recebido_model');
		
		$arq_selecionado  = $this->input->post("arq_selecionado", TRUE);
		$doc_selecionado  = $this->input->post("doc_selecionado", TRUE);
		$part_selecionado = $this->input->post("part_selecionado", TRUE);
		
		$ar_arq  = explode(",",$arq_selecionado);
		$ar_doc  = explode(",",$doc_selecionado);
		$ar_part = array_map("explodePipe", explode(",",$part_selecionado));
		
		$args   = Array();
		$data   = Array();
		$result = null;
		
		$args['ar_tipo'] = array('pdf');
		$args['dir'] = $this->session->userdata('divisao');
		
		$this->Digitalizado_model->listar($result, $args);
		$ar_arq_lista = $result;		
		
		$ar_documento = Array();
		$nr_conta = 0;
		foreach($ar_arq as $id_file)
		{
			foreach($ar_arq_lista as $ar_item)
			{
				if($ar_item['id_file'] == $id_file)
				{
					$arq = md5($ar_item['name']).".".$ar_item['ext'];
					$ar_documento[] = array(
											'cd_documento_recebido_item' => 0,
											'cd_tipo_doc'           => $ar_doc[$nr_conta],
											'cd_empresa'            => $ar_part[$nr_conta][0],
											'cd_registro_empregado' => $ar_part[$nr_conta][1],
											'seq_dependencia'       => $ar_part[$nr_conta][2],
											'nome'                  => $ar_part[$nr_conta][3],
											'ds_observacao'         => "",
											'nr_folha'              => 1,
											'arquivo'               => $arq,
											'arquivo_nome'          => $ar_item['file_name'],
											'cd_usuario_cadastro'   => usuario_id()
					                       );
										   
					#### COPIAR ARQUIVO PARA PASTA ###
					copy($ar_item['file'], "../cieprev/up/documento_recebido/".$arq);
					
					#### REMOVE ARQUIVO ORIGEM ####
					unlink("./".$ar_item['file']);					
				}
			}
			$nr_conta++;
		}
		
		#### GERA PROTOCOLO ####
		if(count($ar_documento) > 0)
		{
			$args = Array();
            $result = null;
			
			$args['cd_documento_recebido']      = 0;
			$args["cd_documento_recebido_tipo"] = 1; #Central de Atendimento
			$args["cd_documento_recebido_tipo_solic"] = $this->input->post("cd_documento_recebido_tipo_solic", TRUE);
			$args["cd_usuario_cadastro"]        = usuario_id();
			$args["cd_usuario"]                 = usuario_id();
			
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);		
			
			if($cd_documento_recebido > 0)
			{
				#### ADICIONA DOCUMENTOS ####				
				foreach($ar_documento as $args)
				{
					$args["cd_documento_recebido"] = $cd_documento_recebido;
					$args["cd_usuario"] = $args['cd_usuario_cadastro'];
					$this->documento_recebido_model->adicionar_documento($result, $args);
				}
				
				redirect( "ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh" );
			}
		}
	}

	public function get_tempo_descarte($cd_documento = 0)
	{
        $args = array();
        $url  = 'http://10.63.255.217:8080/ords/ordsws/tabela_temporalidade_doc/tempo_descarte/index';

        $args = array(
            'id'          => '8dcfac716cf69a12255d63a4abf8b485',
            'cd_tipo_doc' => $cd_documento
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

    	$vl_arquivo_central = (trim($json['result'][0]['vl_arquivo_central']) != '' ? trim($json['result'][0]['vl_arquivo_central']).' - ' : '');
		$ds_tempo_descarte  = $vl_arquivo_central . $json['result'][0]['id_arquivo_central'];
		$id_classificacao_info_doc  = $json['result'][0]['id_classificacao_info_doc'];

        return array('ds_tempo_descarte' => $ds_tempo_descarte, 'id_classificacao_info_doc' => $id_classificacao_info_doc);
	}

	function protocoloDigitalizacao()
	{
		CheckLogin();
		#echo "<PRE>"; print_r($_POST); #exit;
		
		$tp_digitalizacao     = $this->input->post("tp_digitalizacao", TRUE);
		$arq_selecionado      = $this->input->post("arq_selecionado", TRUE);
		$doc_selecionado      = $this->input->post("doc_selecionado", TRUE);
		$part_selecionado     = $this->input->post("part_selecionado", TRUE);
		$proc_selecionado     = $this->input->post("proc_selecionado", TRUE);
		
		$ar_arq      = explode(",",$arq_selecionado);
		$ar_doc      = explode(",",$doc_selecionado);
		$ar_proc     = explode(",",$proc_selecionado);
		$ar_part     = array_map("explodePipe", explode(",",$part_selecionado));
		
		$args   = Array();
		$data   = Array();
		$result = null;
		
		$args['ar_tipo'] = array('pdf');
		$args['dir'] = $this->session->userdata('divisao');
		
		$this->Digitalizado_model->listar($result, $args);
		$ar_arq_lista = $result;		
		
		$ar_documento = Array();
		$nr_conta = 0;
		foreach($ar_arq as $id_file)
		{
			foreach($ar_arq_lista as $ar_item)
			{
				if($ar_item['id_file'] == $id_file)
				{
					$ar_documento[] = array(
											'cd_tipo_doc'           => $ar_doc[$nr_conta],
											'processo'              => ((array_key_exists($nr_conta, $ar_proc)) ? $ar_proc[$nr_conta] : ""),
											'cd_empresa'            => $ar_part[$nr_conta][0],
											'cd_registro_empregado' => $ar_part[$nr_conta][1],
											'seq_dependencia'       => $ar_part[$nr_conta][2],
											'nome'                  => $ar_part[$nr_conta][3],
											'arquivo'               => $ar_item['file'],
											'arquivo_nome'          => $ar_item['name'],
											'arquivo_extensao'      => $ar_item['ext'],
											'arquivo_completo'      => $ar_item['file_name']
					                       );
				}
			}
			$nr_conta++;
		}
		
		#echo "<PRE>".print_r($ar_arq_lista,true)."</PRE>";
		#echo "<PRE>".print_r($ar_arq,true)."</PRE>";
		#echo "<PRE>".print_r($ar_documento,true)."</PRE>";
		#exit;
		
		#### GERA PROTOCOLO ####
		if(count($ar_documento) > 0)
		{
			$this->load->model('projetos/Documento_protocolo_model');

			$tp_protocolo = "";
			switch ($tp_digitalizacao) 
			{
				case "PAR": $tp_protocolo = "GAP"; break;
				case "BEN": $tp_protocolo = "GB"; break;
				case "JUR": $tp_protocolo = "GJ"; break;
			}
			
			if($tp_protocolo == "")		
			{
				echo "ERRO:<BR>Não foi encontrado protocolo para sua área.<BR>Entre em contato com a GI"; exit;
			}			
			
			#### PROTOCOLO DIGITAL ####
			$args['cd_usuario_cadastro'] = intval(usuario_id());
			$args['cd_gerencia']         = $tp_protocolo;
			$args['ano']                 = date('Y');
			$args['tipo_protocolo']      = "D";
			$args['fl_contrato']         = "";
			$ar_protocolo                = Array();
			$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);		

			if(count($ar_protocolo) > 0)
			{
				#### ADICIONA DOCUMENTOS ####
				foreach($ar_documento as $ar_reg)
				{
					$tempo_descarte = $this->get_tempo_descarte($ar_reg['cd_tipo_doc']);
					
					$prot = Array();
					$prot['cd_documento_protocolo']      = intval($ar_protocolo['cd_documento_protocolo']);
					$prot['cd_documento_protocolo_item'] = 0;
					$prot['cd_empresa']                  = $ar_reg['cd_empresa'];
					$prot['cd_registro_empregado']       = $ar_reg['cd_registro_empregado'];
					$prot['seq_dependencia']             = $ar_reg['seq_dependencia'];
					$prot['nr_folha']                    = 1;
					$prot['cd_usuario_cadastro']         = intval(usuario_id());
					$prot['observacao']                  = "";
					$prot['ds_processo']                 = $ar_reg['processo'];	
					$prot['cd_documento']                = $ar_reg['cd_tipo_doc'];
					$prot['cd_tipo_doc']                 = $ar_reg['cd_tipo_doc'];
				
					$arq = $ar_reg['cd_empresa']."_".$ar_reg['cd_registro_empregado']."_".$ar_reg['seq_dependencia']."_".$ar_reg['cd_tipo_doc']."_".uniqid(time()).".pdf";
					$dir = "up/protocolo_digitalizacao_".intval($ar_protocolo['cd_documento_protocolo'])."/";

					$prot['arquivo']                     = $arq;
					$prot['arquivo_nome']                = $arq;	

					
					$prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
					$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);			
					
					#### COPIAR ARQUIVO PARA PASTA ####
					copy($ar_reg['arquivo'], "../cieprev/".$dir.$arq);	

					#### REMOVE ARQUIVO ORIGEM ####
					unlink("./".$ar_reg['arquivo']);
					
					#echo "<PRE>".print_r($prot,true)."</PRE>"; exit;

					if($tp_digitalizacao == "JUR")
					{
						$this->Documento_protocolo_model->adicionar_documento_juridico($result, $prot);
					}
					else
					{
						$this->Documento_protocolo_model->adicionaDocumento($result, $prot);
					}
				}
			}
			
			/*
			if($tp_digitalizacao == "JUR")
			{
				redirect("ecrm/protocolo_digitalizacao/detalhe_juridico/".$ar_protocolo['cd_documento_protocolo'], "refresh");
			}
			elseif($tp_digitalizacao == "PAR")
			{
				redirect("ecrm/protocolo_digitalizacao/detalhe_atendimento/".$ar_protocolo['cd_documento_protocolo'], "refresh");
			}
			elseif($tp_digitalizacao == "BEN")
			{
				redirect("ecrm/protocolo_digitalizacao/detalhe_beneficio/".$ar_protocolo['cd_documento_protocolo'], "refresh");
			}
			*/

			redirect("ecrm/protocolo_digitalizacao/detalhe/".$ar_protocolo['cd_documento_protocolo'], "refresh");

		}
	}

	function excluirDocumentos()
	{
		CheckLogin();
		$args   = Array();
		$data   = Array();
		$result = null;
		
		$arq_selecionado = $this->input->post("arq_selecionado", TRUE);
		$ar_arq          = explode(",",$arq_selecionado);
		$args['ar_tipo'] = array('pdf', 'jpg');
		$args['dir']     = $this->session->userdata('divisao');
		
		$this->Digitalizado_model->listar($result, $args);
		$ar_arq_lista = $result;		
		
		foreach($ar_arq as $id_file)
		{
			foreach($ar_arq_lista as $ar_item)
			{
				if($ar_item['id_file'] == $id_file)
				{
					unlink($ar_item['file']);
				}
			}
		}
		
		redirect("ecrm/digitalizado/", "refresh");
	}	
	
    function notificacao()
    {
    	CheckLogin();

		$ar_arq = Array();
		$args   = Array();
		$data   = Array();
		$result = null;
		
		$args['ar_tipo'] = array('pdf','jpg','png','txt');
		$args['dir']     = $this->session->userdata('divisao');
		
		$this->Digitalizado_model->listar($result, $args);
		$ar_arq = $result;
		

		$data['ar_lista'] = Array();
		if(count($ar_arq) > 0)
		{
			$nr_conta = 0;
			$nr_fim = count($ar_arq);
			while($nr_conta < $nr_fim)
			{
				$args   = Array();
				$result = null;			
				
				$ar_tmp = explode("_", $ar_arq[$nr_conta]['name']);
				$usuario = $ar_tmp[0];
				
				if(strtoupper($usuario) == strtoupper($this->session->userdata('usuario')))
				{
					$ar_arq[$nr_conta]['usuario'] = $usuario;
					
					$data['ar_lista'][] = $ar_arq[$nr_conta];
				}
				$nr_conta++;
			}
		}
		
		$ar_reg = array("qt_doc" => count($data['ar_lista']));

		echo json_encode($ar_reg);
	}

	public function salvar_digitalizado()
	{
		CheckLogin();

		$this->load->model('projetos/digitalizado_model');

		$cd_digitalizado = $this->input->post('cd_digitalizado', TRUE);

		$args = array(
			'id_documento'          => $this->input->post('id_documento', TRUE),
			'cd_documento'          => $this->input->post('cd_documento', TRUE),
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
		    'cd_usuario'            => $this->session->userdata('codigo')
		);

		if(intval($cd_digitalizado) == 0)
		{
			$this->digitalizado_model->salvar_digitalizado($args);
		}
		else
		{
			$this->digitalizado_model->atualizar_digitalizado($cd_digitalizado, $args);
		}
	}	
}
