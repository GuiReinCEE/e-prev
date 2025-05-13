<?php
class cadastro_protocolo_interno extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/documento_recebido_model');
    }

    function index()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['tipo_solicitacao'] = $this->documento_recebido_model->carregar_tipo_solicitacao();

		$this->documento_recebido_model->gerencia($result, $args);
		$data['arr_gerencia'] = $result->result_array();
		
		$this->load->view('ecrm/cadastro_protocolo_interno/index', $data);
    }

    function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args["nr_ano"]                = $this->input->post("nr_ano", TRUE);
		$args["nr_contador"]           = $this->input->post("nr_contador", TRUE);
		$args["dt_cadastro_ini"]       = $this->input->post("dt_cadastro_ini", TRUE);
		$args["dt_cadastro_fim"]       = $this->input->post("dt_cadastro_fim", TRUE);
		$args["cd_status"]             = $this->input->post("cd_status", TRUE);
		$args["cd_gerencia_remetente"] = $this->input->post("cd_gerencia_remetente", TRUE);
		$args["cd_usuario_destino"]    = $this->input->post("cd_usuario_destino", TRUE);
		$args['fl_mostrar_documentos'] = $this->input->post('fl_mostrar_documentos');
		$args["tipo_solicitacao"]      = $this->input->post("tipo_solicitacao");

		manter_filtros($args);

		$args["cd_usuario"] = $this->session->userdata('codigo');

		$this->documento_recebido_model->listar($result, $args);
		$data['collection'] = $result->result_array();


		$this->load->view('ecrm/cadastro_protocolo_interno/partial_result', $data);
    }

    function relatorio($cd_relatorio = '')
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$this->documento_recebido_model->tipo_doc($result, $args);
		$data['tipo_doc_dd'] = $result->result_array();
		
		$this->documento_recebido_model->usuario_envio($result, $args);
		$data['usuario_envio_dd'] = $result->result_array();
		
		$this->documento_recebido_model->usuario_destino($result, $args);
		$data['usuario_destino_dd'] = $result->result_array();
		
		$this->documento_recebido_model->usuario_encerrado($result, $args);
		$data['usuario_encerrado_dd'] = $result->result_array();
		
		$this->documento_recebido_model->gerencia($result, $args);
		$data['arr_gerencia'] = $result->result_array();
		
		$data['cd_documento'] = (trim($cd_relatorio) != '' ? intval($cd_relatorio) : '');

		$data['tipo_solicitacao'] = $this->documento_recebido_model->carregar_tipo_solicitacao();

		$this->load->view('ecrm/cadastro_protocolo_interno/relatorio', $data); 
    }

    function relatorio_lista()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['ano']                   = $this->input->post('nr_ano');
		$args['contador']              = $this->input->post('nr_contador');
		$args['cd_empresa']            = $this->input->post('cd_empresa');
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado');
		$args['seq_dependencia']       = $this->input->post('seq_dependencia');
		$args['nome']                  = $this->input->post('nome_participante');
		$args['cd_tipo_doc']           = $this->input->post('cd_tipo_doc');
		$args['dt_envio_inicio']       = $this->input->post('dt_envio_inicio');
		$args['dt_envio_fim']          = $this->input->post('dt_envio_fim');
		$args['dt_ok_inicio']          = $this->input->post('dt_recebimento_inicio');
		$args['dt_ok_fim']             = $this->input->post('dt_recebimento_fim');
		$args['cd_usuario_envio']      = $this->input->post('cd_usuario_envio');
		$args['cd_usuario_destino']    = $this->input->post('cd_usuario_destino');
		$args['cd_usuario_encerrado']  = $this->input->post('cd_usuario_encerrado');
		$args['fl_encerrado']          = $this->input->post('fl_encerrado');
		$args['fl_enviado']            = $this->input->post('fl_enviado');
		$args['fl_mostrar_documentos'] = $this->input->post('fl_mostrar_documentos');
		$args["cd_gerencia_remetente"] = $this->input->post("cd_gerencia_remetente");
		$args["cd_gerencia_destino"]   = $this->input->post("cd_gerencia_destino");
		$args["tipo_solicitacao"]      = $this->input->post("tipo_solicitacao");
		$args["dt_devolucao_ini"]      = $this->input->post("dt_devolucao_ini");
		$args["dt_devolucao_fim"]      = $this->input->post("dt_devolucao_fim");

		
		manter_filtros($args);

		$this->documento_recebido_model->relatorio($result, $args);
		$data['collection']= $result->result_array();

		$this->load->view('ecrm/cadastro_protocolo_interno/relatorio_partial_result', $data);        
    }

    private function get_permissao($cd_documento_recebido, $row)
    {
		if(intval($row['cd_documento_recebido_grupo_envio']) > 0 AND $this->session->userdata('divisao') == 'GCM')
		{
			$usuario_grupo = $this->documento_recebido_model->get_usuario_grupo($row['cd_documento_recebido_grupo_envio'], $this->session->userdata('codigo'));
			
			if(intval($usuario_grupo['tl']) > 0)
			{
				return TRUE;
			}
			else if($this->session->userdata('codigo') == intval($row['cd_usuario_cadastro']))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else if($this->session->userdata('codigo') == intval($row['cd_usuario_cadastro']))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
    }

    function detalhe($cd = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$row = $this->documento_recebido_model->carregar($cd);

		if (intval($cd) > 0)
		{
			$result = null;
			$args = array();

			$this->documento_recebido_model->recebido_beneficio($result, $args);
			$data['beneficio'] = $result->result_array();
		}

		$data['tipo_solicitacao'] = $this->documento_recebido_model->carregar_tipo_solicitacao();

        $data['row'] = $row;
        
        $data['cd_usuario'] = $this->session->userdata('codigo');

        $data['fl_permissao_cadastro'] = $this->get_permissao($cd, $row);

		
		$this->load->view('ecrm/cadastro_protocolo_interno/detalhe', $data);
    }

    function reabrir($cd_documento_recebido)
    {
        $this->documento_recebido_model->reabrir_documento($cd_documento_recebido);

        redirect("ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh");
    }

    function editar_documento()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_documento_recebido_item'] = $this->input->post('cd_documento_recebido_item');

		$this->documento_recebido_model->editar_documento($result, $args);
		$data = $result->row_array();

		$data = array_map("arrayToUTF8", $data);
		echo json_encode($data);
    }

    function detalhe_grid()
    {
		$args = Array();
		$data = Array();
		$result = null;

        $cd                     = intval($this->input->post('cd'));
        $fl_recebido            = $this->input->post('fl_recebido');
        $fl_tipo_novo_protocolo = $this->input->post('fl_tipo_novo_protocolo');
		
        $data['ar_protocolo'] = $row = $this->documento_recebido_model->carregar($cd, $fl_recebido, $fl_tipo_novo_protocolo);
        $data['row']          = $this->documento_recebido_model->carregar($cd, $fl_recebido, $fl_tipo_novo_protocolo);

        $this->load->view('ecrm/cadastro_protocolo_interno/detalhe_result', $data);
    }

    function excluir_item()
    {
		$args = Array();
		$data = Array();
		$result = null;
	
        $args['cd_documento_recebido_item'] = $this->input->post("cd_documento_recebido_item");
        $args['cd_usuario']                 = $this->session->userdata('codigo');
		
		$this->documento_recebido_model->excluir_item($result, $args);
    }

    function salvar()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_documento_recebido']             = intval($this->input->post('cd_documento_recebido', TRUE));
		$args["cd_documento_recebido_tipo"]        = $this->input->post("cd_documento_recebido_tipo", TRUE);
		$args["cd_usuario"]                         = $this->session->userdata('codigo');
		$args["cd_documento_recebido_tipo_solic"]  = $this->input->post("cd_documento_recebido_tipo_solic", TRUE);
		$args["cd_documento_recebido_grupo_envio"] = '';

		if($this->session->userdata('divisao') == 'GCM')
		{
			$documento_recebido_grupo = $this->documento_recebido_model->get_documento_recebido_grupo($this->session->userdata('codigo'));

			$args["cd_documento_recebido_grupo_envio"] = $documento_recebido_grupo['cd_documento_recebido_grupo'];
		}

		if(intval($args['cd_documento_recebido']) == 0)
		{
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);
		}
		else 
		{
			$cd_documento_recebido = intval($args['cd_documento_recebido']);

			$this->documento_recebido_model->atualizar_protocolo($result, $args);
		}
		

		redirect("ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh");
    }

    function receber()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_documento_recebido'] = intval($this->input->post('cd_documento_recebido'));
		$args['cd_usuario']            = $this->session->userdata('codigo');

		$this->documento_recebido_model->receber_todos_documentos($result, $args);

		$this->documento_recebido_model->receber($result, $args);
    }

    function receber_documento()
    {
		$args = Array();
		$data = Array();
		
		
		$args['cd_documento_recebido_item'] = intval($this->input->post('cd_documento_recebido_item'));
		$args['cd_usuario']                 = $this->session->userdata('codigo');

		$result = null;
		$this->documento_recebido_model->receber_documento($result, $args);
		
		$result = null;
		$this->documento_recebido_model->get_documento_recebido($result, $args);
		
		$ar_doc = $result->row_array();
		echo json_encode($ar_doc);
    }

    function adicionar_documento()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args["cd_documento_recebido"]      = $this->input->post("cd_documento_recebido", true);
		$args["cd_empresa"]                 = $this->input->post("cd_empresa", true);
		$args["cd_registro_empregado"]      = $this->input->post("cd_registro_empregado", true);
		$args["seq_dependencia"]            = $this->input->post("seq_dependencia", true);
		$args["ds_observacao"]              = $this->input->post("ds_observacao", true);
		$args["nr_folha"]                   = $this->input->post("nr_folha", true);
		$args["cd_tipo_doc"]                = $this->input->post("cd_tipo_doc", true);
		$args["cd_usuario"]                 = $this->session->userdata('codigo');
		$args["arquivo"]                    = $this->input->post("arquivo", true);
		$args["nome"]                       = $this->input->post("nome_participante", true);
		$args["arquivo_nome"]               = $this->input->post("arquivo_nome", true);
		$args["cd_documento_recebido_item"] = $this->input->post("cd_documento_recebido_item", true);
		$args['nr_folha_pdf']               = 1;

		$extensao = pathinfo($args['arquivo_nome'], PATHINFO_EXTENSION);
		
		
		if(trim($extensao) == 'pdf')
		{
			if(intval($args["cd_documento_recebido"]) == 0)
			{
				$this->load->plugin('PDFMerger');

				$pdf = new PDFMerger_pi;

				$fpdi = $pdf->fpdi;

				$args['nr_folha_pdf'] = $fpdi->setSourceFile('./up/documento_recebido/'.$args['arquivo']);
			}
		}

		#echo "<PRE>";
		#print_r($args);
		#print_r($extensao);
		#exit;


		$this->documento_recebido_model->adicionar_documento($result, $args);
    }
	
	function resumo()
	{
        $data = Array();
		$args = Array();
        $result = null;
		
		$this->documento_recebido_model->usuario_envio($result, $args);
		$data['usuario_envio_dd'] = $result->result_array();
		
		$this->documento_recebido_model->usuario_destino($result, $args);
		$data['usuario_destino_dd'] = $result->result_array();
		
		$this->documento_recebido_model->usuario_encerrado($result, $args);
		$data['usuario_encerrado_dd'] = $result->result_array();
	
		$this->load->view('ecrm/cadastro_protocolo_interno/resumo', $data);
	}
	
	function resumo_lista()
	{
        $data = Array();
		$args = Array();
        $result = null;
			
		$args['cd_tipo_doc']          = $this->input->post('cd_tipo_doc');
		$args['dt_envio_inicio']      = $this->input->post('dt_envio_inicio');
		$args['dt_envio_fim']         = $this->input->post('dt_envio_fim');
		$args['dt_ok_inicio']         = $this->input->post('dt_recebimento_inicio');
		$args['dt_ok_fim']            = $this->input->post('dt_recebimento_fim');
		$args['cd_usuario_envio']     = $this->input->post('cd_usuario_envio');
		$args['cd_usuario_destino']   = $this->input->post('cd_usuario_destino');
		$args['cd_usuario_encerrado'] = $this->input->post('cd_usuario_encerrado');
		
		manter_filtros($args);

		$this->documento_recebido_model->resumo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/cadastro_protocolo_interno/resumo_result', $data);
	}
	
	function justificar_exclusao($cd_documento_recebido_item)
	{
        $data = Array();
		$args = Array();
        $result = null;
		
		$args['cd_documento_recebido_item'] = $cd_documento_recebido_item;
		
		$this->documento_recebido_model->carrega_informacoes_protocolo($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('ecrm/cadastro_protocolo_interno/justificar_exclusao', $data);
	}
	
	function excluir_justificado()
	{
        $data = Array();
		$args = Array();
        $result = null;
		
		$args['cd_documento_recebido_item'] = $this->input->post('cd_documento_recebido_item');
		$args['justificativa']              = $this->input->post('justificativa');
		$args["cd_usuario"]                 = $this->session->userdata('codigo');
		
		$this->documento_recebido_model->excluir_justificado($result, $args);
		
		redirect("ecrm/cadastro_protocolo_interno", "refresh");
	}
	
	function salvar_obs_recebimento()
	{
		$data = Array();
		$args = Array();
        $result = null;
		
		$args['cd_documento_recebido_item'] = $this->input->post('cd_documento_recebido_item');
		$args['ds_observacao_recebimento']  = $this->input->post('ds_observacao_recebimento');
		$args["cd_usuario"]                 = $this->session->userdata('codigo');
		
		$this->documento_recebido_model->salvar_obs_recebimento($result, $args);
	}
	
	function excluir($cd_documento_recebido = 0)
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_documento_recebido'] = $cd_documento_recebido;
		$args['cd_usuario']            = $this->session->userdata('codigo');
		
		$this->documento_recebido_model->excluir($result, $args);

		redirect('ecrm/cadastro_protocolo_interno', 'refresh');
    }
	
	function beneficio_grid()
    {
		$args = Array();
		$data = Array();
		$result = null;

        $args['beneficio'] = $this->input->post('beneficio');

        $this->documento_recebido_model->tabela_beneficio($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/cadastro_protocolo_interno/seleciona_documento_result', $data);
    }
	
	function inscricao_grid()
    {
		$args = Array();
		$data = Array();
		$result = null;

        $args['cd_plano_empresa'] = $this->input->post('cd_plano_empresa');
        $args['cd_plano']         = $this->input->post('cd_plano');

        $this->documento_recebido_model->tabela_inscricao($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/cadastro_protocolo_interno/seleciona_documento_result', $data);
    }
	
	function enviar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		$msg = array();

		$args['cd_documento_recebido']       = intval($this->input->post('cd_documento_recebido'));
		$args['cd_usuario_envio']            = usuario_id();
		$args['cd_usuario_destino']          = intval($this->input->post('cd_usuario_destino'));
		$args['cd_documento_recebido_grupo'] = intval($this->input->post('cd_documento_recebido_grupo'));
		
		$args['fl_pedido_beneficio']		 = $this->input->post('fl_pedido_beneficio', TRUE);
		$args['fl_certidao_obito']		 	 = $this->input->post('fl_certidao_obito', TRUE);
		$args['fl_doc_indentificacao']		 = $this->input->post('fl_doc_indentificacao', TRUE);
		$args['fl_conta_corrente']			 = $this->input->post('fl_conta_corrente', TRUE);
		$args['fl_ordem_pagamento']			 = $this->input->post('fl_ordem_pagamento', TRUE);
		$args['fl_carta_concessao']			 = $this->input->post('fl_carta_concessao', TRUE); 
		$args['dt_concessao']			 	 = $this->input->post('dt_concessao', TRUE); 
		$args['fl_comprovante_beneficio']	 = $this->input->post('fl_comprovante_beneficio', TRUE);
		$args['fl_certidao_pis']			 = $this->input->post('fl_certidao_pis', TRUE);
		$args['fl_substituto_pis']			 = $this->input->post('fl_substituto_pis', TRUE);
		$args['ds_tipo_documento']			 = $this->input->post('ds_tipo_documento', TRUE);
		$args['fl_nome_titular']			 = $this->input->post('fl_nome_titular', TRUE);
		$args['fl_nome_dependente']			 = $this->input->post('fl_nome_dependente', TRUE);
		$args['fl_situacao'] 				 = $this->input->post('fl_situacao', TRUE);
		$args['fl_pagamento_anterior']		 = $this->input->post('fl_pagamento_anterior', TRUE);
		$args['fl_carimbo']					 = $this->input->post('fl_carimbo', TRUE);
		$args['relacao']					 = $this->input->post('relacao', TRUE);

		if (intval($args['cd_usuario_destino']) == 0 && intval($args['cd_documento_recebido_grupo']) == 0)
		{
			echo "Deve ser informado Usuário de Destino ou Grupo de Destino.";
			return false;
		}
		else
		{
			if($args['relacao'] == 4)
			{
				$this->documento_recebido_model->enviar_documento_pensao($args);
			}
			
			$b = $this->documento_recebido_model->enviar($args, $msg);

			if ($b)
			{
				$email['de'] = 'Protocolo Interno';
				$email['para'] = $this->documento_recebido_model->email_dos_usuarios_de_destino($args['cd_usuario_destino'], $args['cd_documento_recebido_grupo']);
				$email['cc'] = '';
				$email['assunto'] = 'Protocolo Interno - Envio de Documentos';
				$email['cd_evento'] = enum_projetos_eventos::PROTOCOLO_INTERNO_ENVIO;
				$email['mensagem'] = usar_template(
					'protocolo_interno/email_envio.txt', array('{LINK}' => site_url('ecrm/cadastro_protocolo_interno/detalhe/' . $args['cd_documento_recebido']))
				);
				enviar_email($email);

				echo "true";
			}
			else
			{
				foreach ($msg as $it)
				{
					echo $it . "\n";
				}
			}
		}
    }

    function redirecionar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		$msg = array();
		
		$args['cd_documento_recebido']       = intval($this->input->post('cd_documento_recebido'));
		$args['cd_usuario_destino']          = intval($this->input->post('cd_usuario_destino'));
		$args['cd_documento_recebido_grupo'] = intval($this->input->post('cd_documento_recebido_grupo'));

		$b = $this->documento_recebido_model->redirecionar($args, $msg);

		if (intval($args['cd_usuario_destino']) == 0 && intval($args['cd_documento_recebido_grupo']) == 0)
		{
			echo "Deve ser informado Usuário de Destino ou Grupo de Destino.";
			return false;
		}
		else
		{
			if ($b)
			{
				$email['de'] = 'ePrev - Protocolo Interno';
				$email['para'] = $this->documento_recebido_model->email_dos_usuarios_de_destino($args['cd_usuario_destino'], $args['cd_documento_recebido_grupo']);
				$email['cc'] = '';
				$email['assunto'] = 'Protocolo Interno - Envio de Documentos';
				$email['cd_evento'] = enum_projetos_eventos::PROTOCOLO_INTERNO_ENVIO;
				$email['mensagem'] = usar_template(
					'protocolo_interno/email_redirecionamento.txt', array('{LINK}' => site_url('ecrm/cadastro_protocolo_interno/detalhe/' . $args['cd_documento_recebido']))
				);
				enviar_email($email);

				echo "true";
			}
			else
			{
				foreach ($msg as $it)
				{
					echo $it . "\n";
				}
			}
		}
    }
	
	function protocolo_interno_gerar()
    {
		$args = Array();
        $data = Array();
        $result = null;
		
		$this->load->model('projetos/documento_protocolo_model');
		
		$cd_documento_recebido         = intval($this->input->post("cd_documento_recebido", true));
		$args['cd_documento_recebido'] = $cd_documento_recebido;
        $args["ar_proto_selecionado"]  = $this->input->post("ar_proto_selecionado", true);
        $args["cd_documento_recebido_tipo_solic"]  = $this->input->post("cd_documento_recebido_tipo_solic", true);;
		$args['cd_gerencia']           = $this->session->userdata('divisao');
		
		$row = $this->documento_recebido_model->carregar($cd_documento_recebido );

		$args['cd_documento_recebido']             = 0;
		$args["cd_documento_recebido_tipo"]        = $row['cd_documento_recebido_tipo'];
		$args["cd_documento_recebido_grupo_envio"] = $row['cd_documento_recebido_grupo_envio'];
		$args["cd_usuario"]                        = $this->session->userdata('codigo');
		
		$args["cd_documento_recebido"] = $this->documento_recebido_model->incluir_protocolo($result, $args);
		
		$row = $this->documento_recebido_model->carregar($args["cd_documento_recebido"]);
		
		$this->documento_recebido_model->listar_documentos_item($result, $args);
        $ar_doc_lista = $result->result_array();
				
		foreach($ar_doc_lista as $item)
		{
			$args["cd_empresa"]                 = $item['cd_empresa'];
			$args["cd_registro_empregado"]      = $item['cd_registro_empregado'];
			$args["seq_dependencia"]            = $item['seq_dependencia'];
			$args["ds_observacao"]              = $item['ds_observacao'];
			$args["nr_folha"]                   = $item['nr_folha'];
			$args["cd_tipo_doc"]                = $item['cd_tipo_doc'];
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			$args["arquivo"]                    = $item['arquivo'];
			$args["nome"]                       = $item['nome'];
			$args["arquivo_nome"]               = $item['arquivo_nome'];
			$args["cd_documento_recebido_item"] = 0;
			$args['nr_folha_pdf']               = $item['nr_folha_pdf'];

			/*
			$extensao = pathinfo($item['arquivo_nome'], PATHINFO_EXTENSION);

			if(trim($extensao) == 'pdf')
			{
				$this->load->plugin('PDFMerger');

				$pdf = new PDFMerger_pi;

		        $fpdi = $pdf->fpdi;

		        $args['nr_folha_pdf'] = $fpdi->setSourceFile('./up/documento_recebido/'.$item['arquivo']);
			}
			*/

			$this->documento_recebido_model->adicionar_documento($result, $args);
			
			$args["cd_documento_recebido_item"] = $item['cd_documento_recebido_item'];
			$args['ds_observacao']              = 'Adicionado ao protocolo nº '. $row['nr_documento_recebido'];
			
			$this->documento_recebido_model->observacao_novo_protocolo($result, $args);
		}

		redirect("ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh");
	}
	
    function protocolo_digitalizaco_descartar($cd_tipo_doc, $cd_divisao)
    {
        $this->load->model('projetos/documento_protocolo_model');
		
		$result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_tipo_doc'] = intval($cd_tipo_doc);
        $args['cd_divisao']  = trim($cd_divisao);

        $this->documento_protocolo_model->descartar($result, $args);
        $data = $result->row_array();

        if (isset($data['fl_descarte']))
        {
            return $data['fl_descarte'];
        }
        else
        {
            return 'N';
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
	
	function protocolo_gerar()
    {
        $args = Array();
        $data = Array();
        $result = null;
		
		$this->load->model('projetos/documento_protocolo_model');

        $fl_tipo_novo_protocolo       = trim($this->input->post("fl_tipo_novo_protocolo", true));
        $cd_documento_recebido        = intval($this->input->post("cd_documento_recebido", true));
        $args["ar_proto_selecionado"] = $this->input->post("ar_proto_selecionado", true);
		$args['cd_gerencia']          = $this->session->userdata('divisao');

        $this->documento_recebido_model->listar_documentos_item($result, $args);
        $ar_doc_lista = $result->result_array();
		
        if (count($ar_doc_lista) > 0)
        {
            
            $args['cd_usuario_cadastro'] = intval(usuario_id());
            $args['ano']                 = date('Y');
            $args['tipo_protocolo']      = $fl_tipo_novo_protocolo;
            $args['fl_contrato']         = 'N';
            $ar_protocolo = Array();
			
            $this->documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);

            foreach ($ar_doc_lista as $ar_reg)
            {
				$tempo_descarte = $this->get_tempo_descarte($ar_reg['cd_tipo_doc']);
				
                $prot = Array();
                $prot['cd_documento_protocolo']      = intval($ar_protocolo['cd_documento_protocolo']);
                $prot['cd_documento_protocolo_item'] = 0;
                $prot['cd_empresa']                  = $ar_reg['cd_empresa'];
                $prot['cd_registro_empregado']       = $ar_reg['cd_registro_empregado'];
                $prot['seq_dependencia']             = $ar_reg['seq_dependencia'];
                $prot['cd_usuario_cadastro']         = intval(usuario_id());
                $prot['observacao']                  = "";
                $prot['ds_processo']                 = "";
                $prot['cd_documento']                = $ar_reg['cd_tipo_doc'];
				$prot['nr_folha']                    = $ar_reg['nr_folha'];
				$prot['dt_cadastro']                 = $ar_reg['dt_cadastro'];
				$prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
				$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);
				
				if ($fl_tipo_novo_protocolo == "D")
				{
					$ext = pathinfo($ar_reg['arquivo']);

					#### PROTOCOLO DIGITAL ####
					$arq = $ar_reg['cd_empresa'] . "_" . $ar_reg['cd_registro_empregado'] . "_" . $ar_reg['seq_dependencia'] . "_" . $ar_reg['cd_tipo_doc'] . "_" . uniqid(time()) . ".pdf";
					$dir = "up/protocolo_digitalizacao_" . intval($ar_protocolo['cd_documento_protocolo']) . "/";
					
					if(strtolower(trim($ext['extension'])) != 'pdf')
					{
						#### IMAGEM
						$this->load->plugin('fpdf');
				
						$ob_pdf = new PDF();
						$ob_pdf->AddFont('segoeuil');
						$ob_pdf->AddFont('segoeuib');	
						$ob_pdf->SetNrPag(true);
						$ob_pdf->SetMargins(10, 14, 5);
						$ob_pdf->header_exibe = false;
						$ob_pdf->header_logo = false;
						$ob_pdf->header_titulo = false;

						$ob_pdf->SetLineWidth(0);
						$ob_pdf->SetDrawColor(0, 0, 0);

						$margem_x = 10;

						$arq_img = '../cieprev/up/documento_recebido/'.$ar_reg['arquivo'];
						list($w, $h) = getimagesize($arq_img); 

						if($w > $h)
						{
							$lim_width  = 1050;
							$lim_height = 640;	
							$pr_height = ceil(($lim_width * 100) / $w);
							$height = ($pr_height * $h) / 100;					
							$width  = $lim_width;	

							if($height > $lim_height)
							{
								$pr_width = ceil(($lim_height * 100) / $h);
								$width = ($pr_width * $w) / 100;					
								$height  = $lim_height;								
							}
							
							$ob_pdf->AddPage('L');
						}
						else
						{
							$lim_width  = 720;
							$lim_height = 900;
							$pr_width = ceil(($lim_height * 100) / $h);
							$width = ($pr_width * $w) / 100;					
							$height  = $lim_height;						
							
							if($width > $lim_width)
							{
								$pr_height = ceil(($lim_width * 100) / $w);
								$height = ($pr_height * $h) / 100;					
								$width  = $lim_width;							
							}		
							
							$ob_pdf->AddPage('P');
						}

						if($width < $lim_width)
						{
							$margem_x+=  $ob_pdf->ConvertSize(floor(($lim_width - $width) / 2));
						}

						$ob_pdf->Image($arq_img, $margem_x, $ob_pdf->GetY(), $ob_pdf->ConvertSize($width), $ob_pdf->ConvertSize($height),'','',true);

						$ob_pdf->Output("../cieprev/".$dir.$arq);
					}
					else
					{
						#### PDF
						#### COPIAR ARQUIVO PARA PASTA ###
						copy("../cieprev/up/documento_recebido/" . $ar_reg['arquivo'], "../cieprev/" . $dir . $arq);	
					}	

					$prot['fl_descartar'] = "N";
					$prot['arquivo']      = $arq;
					$prot['arquivo_nome'] = $arq;					
				}
				else
				{
					$prot['fl_descartar'] = $this->protocolo_digitalizaco_descartar($ar_reg['cd_tipo_doc'], $this->session->userdata('divisao'));
					$prot['arquivo']      = "";
					$prot['arquivo_nome'] = "";
				}
					
                $this->documento_protocolo_model->adicionaDocumento($result, $prot);
            }

            redirect("ecrm/protocolo_digitalizacao/detalhe_atendimento/" . $ar_protocolo['cd_documento_protocolo'], "refresh");
        }
        else
        {
            redirect("ecrm/cadastro_protocolo_interno/detalhe/" . $cd_documento_recebido, "refresh");
        }
    }
	
	function devolver($cd_protocolo_interno = 0)
    {
        $args = Array();
        $data = Array();
        $result = null;
		
        if (intval($cd_protocolo_interno) > 0)
        {
            $data['ar_protocolo'] = $this->documento_recebido_model->carregar(intval($cd_protocolo_interno));
            $this->load->view('ecrm/cadastro_protocolo_interno/devolver', $data);
        }
        else
        {
            exibir_mensagem("PROTOCOLO INTERNO NÃO INFORMADO");
        }
    }
	
	function salvar_devolucao()
    {
        $data = Array();
        $args = Array();
        $result = null;

        $args["cd_documento_recebido"] = $this->input->post("cd_documento_recebido", TRUE);
        $args["descricao"]             = $this->input->post("descricao", TRUE);
        $args["cd_usuario"]            = $this->session->userdata('codigo');

        $this->documento_recebido_model->salvar_devolucao($result, $args);
		
        redirect("ecrm/cadastro_protocolo_interno/detalhe/" . intval($args["cd_documento_recebido"]), "refresh");
    }
	
    function salvar_re()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_documento_recebido_item'] = intval($this->input->post('cd_documento_recebido_item', TRUE));
		$args["cd_empresa"]                 = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"]      = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]            = $this->input->post("seq_dependencia", TRUE);
		$args["nome"]                       = $this->input->post("nome", TRUE);
		$args["cd_usuario"]                 = $this->session->userdata('codigo');

		$this->documento_recebido_model->salvar_re($result, $args);
    }	
}
?>