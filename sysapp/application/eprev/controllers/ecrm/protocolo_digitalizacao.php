<?php
class protocolo_digitalizacao extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/documento_protocolo_model');
		
		ini_set('max_execution_time', 0);
    }

    public function get_permissao_digitalizacao()
    {
        #Silvana Francisco Oliveira
        if ($this->session->userdata('codigo') == 79) 
        {
            return true;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251) 
        {
            return true;
        }
        else
        {
            return false;
        }
    }
	
	private function carrega_campo_cadastro($cd_documento_protocolo)
	{
		$args['cd_documento_protocolo'] = intval($cd_documento_protocolo);
		
		if($args['cd_documento_protocolo'] == 0)
		{
			$row = array(
				'cd_documento_protocolo' => $args['cd_documento_protocolo'],
				'ano'                    => '',
				'tipo'                   => '',
				'contador'               => '',
				'dt_cadastro'            => '',
				'cd_usuario_cadastro'    => '',
				'dt_envio'               => '',
				'cd_usuario_envio'       => '',
				'dt_ok'                  => '',
				'cd_usuario_ok'          => '',
				'dt_exclusao'            => '',
				'cd_usuario_exclusao'    => '',
				'motivo_exclusao'        => '',
				'ordem_itens'            => '',
				'dt_indexacao'           => '',
				'cd_usuario_indexacao'   => '',
				'nome_usuario_cadastro'  => '',
				'nome_usuario_envio'     => '',
				'nome_usuario_ok'        => '',
				'nome_usuario_indexacao' => ''
			);
		}
		else
		{
			$this->documento_protocolo_model->carregar($result, $args);
			$row = $result->row_array();
		}
		
		return $row;
	}

    function index($nr_ano = '', $nr_sequencia = '')
    {
        $data   = array();
        $args   = array();
        $result = null;

        $data = array(
            'nr_ano'       => (intval($nr_ano) > 0 ? $nr_ano : ''),
            'nr_sequencia' => $nr_sequencia
        );

        $this->documento_protocolo_model->get_usuario_envio($result, $args);
        $data['usuario_envio'] = $result->result_array();

        $data['gerencia'] = $this->documento_protocolo_model->get_gerencia_envio();

        $this->load->view('ecrm/protocolo_digitalizacao/index', $data);
    }

    function listar()
    {
        $data   = array();
        $args   = array();
        $result = null;

        $args["gerencia_responsavel_recebimento"] = 'GFC';
        $args["ano"]                              = intval($this->input->post("ano", true));
        $args["contador"]                         = intval($this->input->post("contador", true));
        $args["cd_usuario_logado"]                = intval(usuario_id());
        $args["gerencia_usuario_logado"]          = $this->session->userdata('divisao');
        $args["tipo_protocolo"]                   = $this->input->post("tipo_protocolo", true);
        $args["fl_envio"]                         = $this->input->post("fl_envio", true);
        $args["fl_recebido"]                      = $this->input->post("fl_recebido", true);
        $args["dt_inclusao_ini"]                  = $this->input->post("dt_inclusao_ini", true);
        $args["dt_inclusao_fim"]                  = $this->input->post("dt_inclusao_fim", true);
        $args["dt_envio_ini"]                     = $this->input->post("dt_envio_ini", true);
        $args["dt_envio_fim"]                     = $this->input->post("dt_envio_fim", true);
        $args["dt_recebido_ini"]                  = $this->input->post("dt_recebido_ini", true);
        $args["dt_recebido_fim"]                  = $this->input->post("dt_recebido_fim", true);
        $args["cd_usuario_envio"]                 = $this->input->post("cd_usuario_envio", true);
        $args["cd_gerencia"]                      = $this->input->post("cd_gerencia", true);

        manter_filtros($args);

        $this->documento_protocolo_model->listar($result, $args);
        $data['collection'] = $result->result_array();
		
		foreach($data['collection'] as $key => $item)
		{
			$array_info = $this->documento_protocolo_model->get_info_item($item['cd_documento_protocolo']);
			
			$data['collection'][$key]['obs']    = array();
            $data['collection'][$key]['liquid'] = array();
			
			foreach($array_info as $key2 => $item2)
			{
                $data['collection'][$key]['obs'][$key2]    = $item2['observacao'];
				$data['collection'][$key]['liquid'][$key2] = $item2['ds_caminho_liquid'];
			}
		}

        $data['gerencia_responsavel_recebimento'] = $args["gerencia_responsavel_recebimento"];
		
        $this->load->view('ecrm/protocolo_digitalizacao/partial_result', $data);     
    }

    function detalhe($cd_documento_protocolo = 0)
    {
		if ($this->session->userdata('divisao') == "GJ")
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_aj/'.$cd_documento_protocolo, 'refresh');
        }		
        elseif (($this->session->userdata('divisao') == "GP") AND (in_array($this->session->userdata('divisao_ant'), array("GJ"))))
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_juridico/'.$cd_documento_protocolo, 'refresh');
        }
        elseif (($this->session->userdata('divisao') == "GAP.") AND (in_array($this->session->userdata('divisao_ant'), array("GB"))))
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_beneficio/'.$cd_documento_protocolo, 'refresh');
        }
		elseif ($this->session->userdata('divisao') == "GNR")
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_atendimento/'.$cd_documento_protocolo, 'refresh');
        }
        elseif (($this->session->userdata('divisao') == "GS") AND (in_array($this->session->userdata('divisao_ant'), array("GAD"))))
        {
            /*
            $fl_contrato = 'N';

            if(intval($cd_documento_protocolo) > 0)
            {
                $result = null;

                $args['cd_documento_protocolo'] = $cd_documento_protocolo;

                $this->documento_protocolo_model->carregar($result, $args);
                $row = $result->row_array();

                $fl_contrato = $row['fl_contrato'];
            }
            */

            redirect('ecrm/protocolo_digitalizacao/detalhe_adm_contrato/'.$cd_documento_protocolo, 'refresh');
        }
        else if(($this->session->userdata('divisao') == "GS"))
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_administrativo/'.$cd_documento_protocolo, 'refresh');
        }
		
		elseif ($this->session->userdata('divisao') == "GFC")
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_financeiro/'.$cd_documento_protocolo, 'refresh');
        }
		elseif  (($this->session->userdata('divisao') == "GC") AND (in_array($this->session->userdata('divisao_ant'), array("SG"))))
		{
			redirect('ecrm/protocolo_digitalizacao/detalhe_secretaria/'.$cd_documento_protocolo, 'refresh');
		}
		elseif (gerencia_in(array('GC')))
		{
			redirect('ecrm/protocolo_digitalizacao/detalhe_controladoria/'.$cd_documento_protocolo, 'refresh');
		}
        else
        {
            redirect('ecrm/protocolo_digitalizacao/detalhe_padrao/'.$cd_documento_protocolo, 'refresh');
        }
    }

    function detalhe_padrao($cd_documento_protocolo)
    {
        $data   = array();
        $args   = array();
        $result = null;
        
        $data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
        
        $this->load->view('ecrm/protocolo_digitalizacao/detalhe_padrao', $data);
    }

    function detalhe_aj($cd_documento_protocolo)
    {
        if (gerencia_in(array('GJ')))
        {           
            $data   = array();
            $args   = array();
            $result = null;
            
            $data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
            
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_aj', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function detalhe_juridico($cd_documento_protocolo)
    {
		if (gerencia_in(array('GJ')))
        {			
			$data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_juridico', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function detalhe_beneficio($cd_documento_protocolo)
    {
        if (gerencia_in(array('GAP.')))
        {
            $data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_beneficio', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function detalhe_atendimento($cd_documento_protocolo)
    {
        if (gerencia_in(array('GNR')))
        {
            $data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_atendimento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function detalhe_administrativo($cd_documento_protocolo)
    {
        if (gerencia_in(array('GS')))
        {
            $data   = array();
            $args   = array();
            $result = null;
            
            $data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
            
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_administrativo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function detalhe_adm_contrato($cd_documento_protocolo)
    {
        if (gerencia_in(array('GS')))
        {
            $data   = array();
            $args   = array();
            $result = null;
            
            $data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
            
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_adm_contrato', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function detalhe_financeiro($cd_documento_protocolo)
    {
        if (gerencia_in(array('GFC')))
        {
            $data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_financeiro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	function detalhe_secretaria($cd_documento_protocolo)
    {
        if (gerencia_in(array('GRC')))
        {
            $data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_secretaria', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	function detalhe_controladoria($cd_documento_protocolo)
    {
        if (gerencia_in(array('GC')))
        {
            $data   = array();
			$args   = array();
			$result = null;
			
			$data['row'] = $this->carrega_campo_cadastro($cd_documento_protocolo);
			
            $this->load->view('ecrm/protocolo_digitalizacao/detalhe_controladoria', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function criar_protocolo()
    {
        $msg = array();
        $row = array();

        $args['cd_usuario_cadastro'] = intval(usuario_id());
        $args['ano']                 = date('Y');
        $args['tipo_protocolo']      = trim($this->input->post('tipo_protocolo'));
        $args['fl_contrato']         = trim($this->input->post('fl_contrato'));
		$args['cd_gerencia']         = (trim($this->input->post('cd_gerencia')) != "") ? trim($this->input->post('cd_gerencia')) : $this->session->userdata('divisao');

        $retorno = $this->documento_protocolo_model->criar_protocolo($args, $msg, $row);

        if ($retorno)
        {
            echo json_encode($row);
        }
        else
        {
            $mensagens = implode('<br>', $msg);
            exibir_mensagem($msg[0]);
        } 
    }

    function enviar_protocolo()
    {
        $args['cd_documento_protocolo'] = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_usuario_envio']       = intval(usuario_id());
        
        if (intval($args['cd_documento_protocolo']) > 0)
        {
            $this->documento_protocolo_model->enviar_protocolo($args);
            echo 'true';
        }
        else
        {
            echo 'false';
        }
    }
	
	function excluir_protocolo($cd_documento_protocolo)
	{
		$result = null;
        $data = Array();
        $args = Array();
		
		$args['cd_documento_protocolo'] = $cd_documento_protocolo;
        $args['cd_usuario']             = $this->session->userdata('codigo');
		
		$this->documento_protocolo_model->excluir_protocolo($result, $args);
		
		redirect('ecrm/protocolo_digitalizacao', 'refresh');
	}

    function adicionar_documentos_por_processo()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        $args['cd_usuario_cadastro']    = intval(usuario_id());
        $args['cd_processo']            = $this->input->post('cd_processo');
        $args['dt_inicio']              = $this->input->post('dt_inicio');
        $args['dt_fim']                 = $this->input->post('dt_fim');
        $args['cd_documento']           = $this->input->post('cd_documento');
        $args['cd_carta_precatoria']    = $this->input->post('cd_carta_precatoria');
        
        $this->documento_protocolo_model->adicionar_documentos_por_processo($result, $args);
        echo 'true';
        
    }

    function adicionar_documento_juridico()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args["cd_documento_protocolo_item"] = $this->input->post('cd_documento_protocolo_item');
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        $args['cd_empresa']             = $this->input->post('cd_empresa');
        $args['cd_registro_empregado']  = $this->input->post('cd_registro_empregado');
        $args['seq_dependencia']        = $this->input->post('seq_dependencia');
        $args['cd_usuario_cadastro']    = usuario_id();
        $args['observacao']             = $this->input->post('observacao');
        $args['ds_processo']            = $this->input->post('ds_processo');
        $args['nr_folha']               = $this->input->post('nr_folha');
        $args['arquivo']                = $this->input->post('arquivo');
        $args['arquivo_nome']           = $this->input->post('arquivo_nome');
        $args['fl_descartar']           = $this->input->post('fl_descartar');
        $args['cd_tipo_doc']            = $this->input->post('cd_tipo_doc');
        $args['cd_documento']           = $this->input->post('cd_tipo_doc');
        $args['ds_caminho_liquid']      = $this->input->post('ds_caminho_liquid');
        
        $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }

        $this->documento_protocolo_model->adicionar_documento_juridico($result, $args);
        echo 'true';
    }

    function adicionar_documento_aj()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args["cd_documento_protocolo_item"] = $this->input->post('cd_documento_protocolo_item');
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        $args['cd_empresa']             = $this->input->post('cd_empresa');
        $args['cd_registro_empregado']  = $this->input->post('cd_registro_empregado');
        $args['seq_dependencia']        = $this->input->post('seq_dependencia');
        $args['nome_participante']      = $this->input->post('nome_participante');
        $args['cd_usuario_cadastro']    = usuario_id();
        $args['observacao']             = $this->input->post('observacao');
        $args['ds_processo']            = $this->input->post('ds_processo');
        $args['nr_folha']               = $this->input->post('nr_folha');
        $args['arquivo']                = $this->input->post('arquivo');
        $args['arquivo_nome']           = $this->input->post('arquivo_nome');
        $args['fl_descartar']           = $this->input->post('fl_descartar');
        $args['cd_tipo_doc']            = $this->input->post('cd_tipo_doc');
        $args['cd_documento']           = $this->input->post('cd_tipo_doc');
        $args['ds_caminho_liquid']      = $this->input->post('ds_caminho_liquid');
        
        $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }

        $this->documento_protocolo_model->adicionar_documento_aj($result, $args);
        echo 'true';
    }

    function adicionar_documento()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_documento_protocolo_item'] = intval($this->input->post('cd_documento_protocolo_item'));
        $args['cd_documento_protocolo']      = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_empresa']                  = intval($this->input->post('cd_empresa'));
        $args['cd_registro_empregado']       = intval($this->input->post('cd_registro_empregado'));
        $args['seq_dependencia']             = intval($this->input->post('seq_dependencia'));
        $args['nr_folha']                    = intval($this->input->post('nr_folha'));
        $args['cd_documento']                = intval($this->input->post('cd_documento'));
        $args['cd_usuario_cadastro']         = intval(usuario_id());
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['arquivo']                     = $this->input->post('arquivo');
        $args['arquivo_nome']                = $this->input->post('arquivo_nome');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['banco']                       = $this->input->post('banco');
        $args['caminho']                     = $this->input->post('caminho');
        $args['ds_caminho_liquid']           = $this->input->post('ds_caminho_liquid');
        $args['fl_tipo_protocolo_contrato']  = $this->input->post('fl_tipo_protocolo_contrato');
        $args['nr_ano_contrato']             = $this->input->post('nr_ano_contrato');
        $args['nr_id_contrato']              = $this->input->post('nr_id_contrato');
        $args['ds_tempo_descarte'] 			 = '';

/*
        $fl_descarte = (trim($this->input->post('fl_descarte', TRUE)) != 'N' ? TRUE : FALSE);
        
        if($fl_descarte)
        {
        	$ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        	$args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($this->get_tempo_descarte($args['cd_documento'])));
        }

        */
        if(intval($args['cd_documento']) > 0)
        {
            $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
			$ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
			$args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
			
			$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

            if($args['ds_tempo_descarte'] == 'PERMANENTE')
            {
                $args['fl_descartar'] = 'N';
            }
            else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
            {
                $args['fl_descartar'] = 'S';
            }
        }

        $this->documento_protocolo_model->adicionaDocumento($result, $args);
        echo 'true';
    }

    function adicionar_documento_adm()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $qt_arquivo = intval($this->input->post("arquivo_m", TRUE));

        $args['cd_documento_protocolo_item'] = intval($this->input->post('cd_documento_protocolo_item'));
        $args['cd_documento_protocolo']      = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_empresa']                  = intval($this->input->post('cd_empresa'));
        $args['cd_registro_empregado']       = intval($this->input->post('cd_registro_empregado'));
        $args['seq_dependencia']             = intval($this->input->post('seq_dependencia'));
        $args['nr_folha']                    = intval($this->input->post('nr_folha'));
        $args['cd_documento']                = intval($this->input->post('cd_documento'));
        $args['cd_usuario_cadastro']         = intval(usuario_id());
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['banco']                       = $this->input->post('banco');
        $args['caminho']                     = $this->input->post('caminho');
        $args['ds_caminho_liquid']           = $this->input->post('ds_caminho_liquid');

        $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }
        
        $this->documento_protocolo_model->carregar($result, $args);
        $row = $result->row_array();
        
        if(trim($row['tipo']) == 'P')
        {
            $args['arquivo_nome']  = '';
            $args['arquivo']       = '';
        
            $this->documento_protocolo_model->adicionar_documento_adm($result, $args);
        }
        else
        {
            if($qt_arquivo > 0)
            {
                $nr_conta = 0;
                while($nr_conta < $qt_arquivo)
                {
                    $result = null;
                    $data = Array();    
                    
                    $args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
                    $args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);

                    $this->documento_protocolo_model->adicionar_documento_adm($result, $args);
                    
                    $nr_conta++;
                }
            }
        }
        
        echo 'true';
    }

    function adicionar_documento_padrao()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_documento_protocolo_item'] = $this->input->post('cd_documento_protocolo_item');
        $args['cd_documento_protocolo']      = $this->input->post('cd_documento_protocolo');
        $args['cd_empresa']                  = $this->input->post('cd_empresa');
        $args['cd_registro_empregado']       = $this->input->post('cd_registro_empregado');
        $args['seq_dependencia']             = $this->input->post('seq_dependencia');
        $args['nr_folha']                    = $this->input->post('nr_folha');
        $args['cd_documento']                = $this->input->post('cd_documento');
        $args['cd_usuario_cadastro']         = usuario_id();
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['arquivo']                     = $this->input->post('arquivo');
        $args['arquivo_nome']                = $this->input->post('arquivo_nome');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['ds_caminho_liquid']           = $this->input->post('ds_caminho_liquid');
        
        $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }

        $this->documento_protocolo_model->adicionar_documento_padrao($result, $args);
        echo 'true';
    }
	
	function adicionar_documento_controladoria()
    {
        $result = null;
        $data = Array();
        $args = Array();
		
		$qt_arquivo = intval($this->input->post("arquivo_m", TRUE));
		
		$args['cd_documento_protocolo_item'] = intval($this->input->post('cd_documento_protocolo_item'));
        $args['cd_documento_protocolo']      = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_empresa']                  = intval($this->input->post('cd_empresa'));
        $args['cd_registro_empregado']       = intval($this->input->post('cd_registro_empregado'));
        $args['seq_dependencia']             = intval($this->input->post('seq_dependencia'));
        $args['nr_folha']                    = intval($this->input->post('nr_folha'));
        $args['cd_documento']                = intval($this->input->post('cd_documento'));
        $args['cd_usuario_cadastro']         = intval(usuario_id());
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['banco']                       = $this->input->post('banco');
        $args['caminho']                     = $this->input->post('caminho');
        $args['ds_caminho_liquid']      	 = $this->input->post('ds_caminho_liquid');
        
	    $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }
		
		$this->documento_protocolo_model->carregar($result, $args);
		$row = $result->row_array();
		
		if(trim($row['tipo']) == 'P')
		{
			$args['arquivo_nome']  = '';
			$args['arquivo']       = '';
		
			$this->documento_protocolo_model->adicionaDocumento($result, $args);
		}
		else
		{
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;
				while($nr_conta < $qt_arquivo)
				{
					$result = null;
					$data = Array();	
					
					$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
					$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);

					$this->documento_protocolo_model->adicionaDocumento($result, $args);
					
					$nr_conta++;
				}
			}
		}
		
        echo 'true';
    }

    function adicionar_documento_financeiro()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $qt_arquivo = intval($this->input->post("arquivo_m", TRUE));
        
        $args['cd_documento_protocolo_item'] = intval($this->input->post('cd_documento_protocolo_item'));
        $args['cd_documento_protocolo']      = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_empresa']                  = intval($this->input->post('cd_empresa'));
        $args['cd_registro_empregado']       = intval($this->input->post('cd_registro_empregado'));
        $args['seq_dependencia']             = intval($this->input->post('seq_dependencia'));
        $args['nr_folha']                    = intval($this->input->post('nr_folha'));
        $args['cd_documento']                = intval($this->input->post('cd_documento'));
        $args['cd_usuario_cadastro']         = intval(usuario_id());
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['banco']                       = $this->input->post('banco');
        $args['caminho']                     = $this->input->post('caminho');
        $args['ds_caminho_liquid']      = $this->input->post('ds_caminho_liquid');

        $tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }
        
        $this->documento_protocolo_model->carregar($result, $args);
        $row = $result->row_array();
        
        if(trim($row['tipo']) == 'P')
        {
            $args['arquivo_nome']  = '';
            $args['arquivo']       = '';
        
            $this->documento_protocolo_model->adicionaDocumento($result, $args);
        }
        else if(trim($row['tipo']) == 'D' AND $qt_arquivo == 0)
        {
            $this->documento_protocolo_model->adicionaDocumentoSemDocumento($result, $args);
        }
        else
        {
            if($qt_arquivo > 0)
            {
                $nr_conta = 0;
                while($nr_conta < $qt_arquivo)
                {
                    $result = null;
                    $data = Array();    
                    
                    $args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
                    $args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);

                    $this->documento_protocolo_model->adicionaDocumento($result, $args);
                    
                    $nr_conta++;
                }
            }
        }
        
        echo 'true';
    }

    function adicionar_documento_beneficio()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $qt_arquivo = intval($this->input->post("arquivo_m", TRUE));
        
        $args['cd_documento_protocolo_item'] = intval($this->input->post('cd_documento_protocolo_item'));
        $args['cd_documento_protocolo']      = intval($this->input->post('cd_documento_protocolo'));
        $args['cd_empresa']                  = intval($this->input->post('cd_empresa'));
        $args['cd_registro_empregado']       = intval($this->input->post('cd_registro_empregado'));
        $args['seq_dependencia']             = intval($this->input->post('seq_dependencia'));
        $args['nr_folha']                    = intval($this->input->post('nr_folha'));
        $args['cd_documento']                = intval($this->input->post('cd_documento'));
        $args['cd_usuario_cadastro']         = intval(usuario_id());
        $args['observacao']                  = $this->input->post('observacao');
        $args['ds_processo']                 = $this->input->post('ds_processo');
        $args['fl_descartar']                = $this->input->post('fl_descartar');
        $args['banco']                       = $this->input->post('banco');
        $args['caminho']                     = $this->input->post('caminho');
        $args['ds_caminho_liquid']           = $this->input->post('ds_caminho_liquid');
        
		$tempo_descarte = $this->get_tempo_descarte($args['cd_documento']);
		
        $ds_tempo_descarte 			= utf8_decode($this->input->post('ds_tempo_descarte'));
        $args['ds_tempo_descarte']  = (trim($ds_tempo_descarte) != '' ? trim($ds_tempo_descarte) : utf8_decode($tempo_descarte['ds_tempo_descarte']));
		
		$args['id_classificacao_info_doc'] = utf8_decode($tempo_descarte['id_classificacao_info_doc']);

        if($args['ds_tempo_descarte'] == 'PERMANENTE')
        {
            $args['fl_descartar'] = 'N';
        }
        else if($args['ds_tempo_descarte'] == 'NAO ARQUIVADO')
        {
            $args['fl_descartar'] = 'S';
        }
        
        $this->documento_protocolo_model->carregar($result, $args);
        $row = $result->row_array();
        
        if(trim($row['tipo']) == 'P')
        {
            $args['arquivo_nome']  = '';
            $args['arquivo']       = '';
        
            $this->documento_protocolo_model->adicionaDocumento($result, $args);
        }
        else if(trim($row['tipo']) == 'D' AND $qt_arquivo == 0)
        {
            $this->documento_protocolo_model->adicionaDocumentoSemDocumento($result, $args);
        }
        else
        {
            if($qt_arquivo > 0)
            {
                $nr_conta = 0;
                while($nr_conta < $qt_arquivo)
                {
                    $result = null;
                    $data = Array();    
                    
                    $args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
                    $args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);

                    $this->documento_protocolo_model->adicionaDocumento($result, $args);
                    
                    $nr_conta++;
                }
            }
        }
        
        echo 'true';
    }

    function listar_documento_aj()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_aj($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/aj_result', $data);          
    }

    function listar_documento_juridico()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_juridico($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/juridico_result', $data);          
    }

    function listar_documento()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
       
        $this->documento_protocolo_model->listar_documento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/beneficio_result', $data);  
    }

    function listar_documento_atendimento()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_atendimento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/atendimento_result', $data);  
    }

    function listar_documento_administrativo()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_atendimento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/administrativo_result', $data);  
    }
    
    function listar_documento_financeiro()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_atendimento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/financeiro_result', $data);  
    }

    function listar_documento_padrao()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_atendimento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/padrao_result', $data);  
    }
	
	function listar_documento_secretaria()
	{
		$result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_secretaria($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/secretaria_result', $data);  
	}
	
	function listar_adm_contrato()
	{
		$result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_adm_contrato($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/adm_contrato_result', $data);  
	}

    function listar_documento_controladoria()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->listar_documento_controladoria($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/protocolo_digitalizacao/controladoria_result', $data);  
    }
	
    function gc_banco_autocomplete()
    {
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['banco'] = '';
		
		if($this->input->post("term", TRUE) != "")
		{
		   $args["banco"] = $this->input->post("term", TRUE);
		}
		
		$this->documento_protocolo_model->gc_banco_autocomplete($result, $args);
        $ar_reg = $result->result_array();
		
		$ar_data = Array();
		
		foreach($ar_reg as $ar_item)
		{
		   $ar_data[] = array("label" => $ar_item['banco'], "value" => $ar_item['banco']);
		}

		echo json_encode($ar_data);
    }

    function gc_caminho_autocomplete()
    {
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['caminho'] = '';
		
		if($this->input->post("term", TRUE) != "")
		{
		   $args["caminho"] = $this->input->post("term", TRUE);
		}
		
		$this->documento_protocolo_model->gc_caminho_autocomplete($result, $args);
        $ar_reg = $result->result_array();
		
		$ar_data = Array();
		
		foreach($ar_reg as $ar_item)
		{
		   $ar_data[] = array("label" => $ar_item['caminho'], "value" => $ar_item['caminho']);
		}

		echo json_encode($ar_data);
    }	
	
	/*
    function excluir($id)
    {
        $this->documento_protocolo_model->excluir($id);

        redirect('ecrm/protocolo_digitalizacao', 'refresh');
    }
	*/
	
    function excluir_documento()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_usuario_exclusao']         = usuario_id();
        $args['cd_documento_protocolo_item'] = $this->input->post('cd_documento_protocolo_item');
        
        $this->documento_protocolo_model->excluir_documento($result, $args);

        echo 'true';
    }

    function descartar()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_tipo_doc'] = intval($this->input->post('cd_tipo_doc'));
        $args['cd_divisao']  = trim($this->input->post('cd_divisao'));

        $this->documento_protocolo_model->descartar($result, $args);
        $data = $result->row_array();

        if (isset($data['fl_descarte']))
        {
            echo $data['fl_descarte'];
        }
        else
        {
            echo 'N';
        }
    }

    function editar_documento()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_documento_protocolo_item'] = $this->input->post('cd_documento_protocolo_item');
       
        $this->documento_protocolo_model->editar_documento($result, $args);
        $data = $result->row_array();

        $data = array_map("arrayToUTF8", $data);
        echo json_encode($data);
    }

    function excluir_todos_documentos()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_usuario_exclusao']    = usuario_id();
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo');
        
        $this->documento_protocolo_model->excluir_todos_documentos($result, $args);
        
        echo 'true';
    }

    function relatorio($ano = '', $seq = '')
    {
        $args = array();
        $data = array();
        $result = null;

        $this->documento_protocolo_model->usuario_combo($result, $args);
        $data['usuario_envio_dd'] = $result->result_array();

        $data['mes_ano_indicador'] = $this->documento_protocolo_model->get_mes_ano_indicador();

        if(trim($ano) != '')
        {
            $data['ano_filtro'] = $ano;
        }
        else
        {
            $data['ano_filtro'] = date('Y');
        }
        
        $data['seq_filtro'] = $seq;

        $this->load->view('ecrm/protocolo_digitalizacao/relatorio.php', $data);
    }

    function relatorio_lista()
    {
        $args = array();
        $data = array();
        $result = null;

        $args['ds_mes_ano_indicador'] = $this->input->post('ds_mes_ano_indicador', true);

        if(trim($args['ds_mes_ano_indicador']) == '')
        {
            $args['ano']                  = $this->input->post('nr_ano', true);
            $args['dt_ok_inicio']         = $this->input->post('dt_recebimento_inicio', true);
            $args['dt_ok_fim']            = $this->input->post('dt_recebimento_fim', true);
            $args['contador']              = $this->input->post('nr_contador', true);
            $args['tipo_protocolo']        = $this->input->post('tipo_protocolo', true);
            $args['cd_empresa']            = $this->input->post('cd_empresa', true);
            $args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', true);
            $args['seq_dependencia']       = $this->input->post('seq_dependencia', true);
            $args['nome']                  = $this->input->post('nome_participante', true);
            $args['cd_tipo_doc']           = $this->input->post('cd_tipo_doc', true);
            $args['cd_doc_juridico']       = $this->input->post('cd_doc_juridico', true);
            $args['dt_envio_inicio']       = $this->input->post('dt_envio_inicio', true);
            $args['dt_envio_fim']          = $this->input->post('dt_envio_fim', true);
            $args['cd_usuario_envio']      = $this->input->post('cd_usuario_envio', true);
            $args['cd_usuario_destino']    = $this->input->post('cd_usuario_destino', true);
            $args['ds_processo']           = $this->input->post('ds_processo', true);

            $qt_doc_recebido = 0;
            $qt_doc_fora     = 0;
        }
        else
        {
            $args['ano']                   = '';
            $args['dt_ok_inicio']          = '';
            $args['dt_ok_fim']             = '';
            $args['contador']              = '';
            $args['tipo_protocolo']        = '';
            $args['cd_empresa']            = '';
            $args['cd_registro_empregado'] = '';
            $args['seq_dependencia']       = '';
            $args['nome']                  = '';
            $args['cd_tipo_doc']           = '';
            $args['cd_doc_juridico']       = '';
            $args['dt_envio_inicio']       = '';
            $args['dt_envio_fim']          = '';
            $args['cd_usuario_envio']      = '';
            $args['cd_usuario_destino']    = '';
            $args['ds_processo']           = '';

            $row = $this->documento_protocolo_model->total($args['ds_mes_ano_indicador']);

            $qt_doc_recebido = $row['total'];

            $row = $this->documento_protocolo_model->fora_prazo($args['ds_mes_ano_indicador']);

            $qt_doc_fora     = $row['total'];
        }

        $args['qt_pagina']         = $this->input->post('qt_pagina', true);
        $args['nr_pagina']         = $this->input->post('nr_pagina', true);

		$args["cd_usuario_logado"] = intval($this->session->userdata('codigo'));

        manter_filtros($args);

        $this->documento_protocolo_model->relatorio($result, $args);
        $data['collection'] = $result->result_array();

        $data['ds_mes_ano_indicador'] = $args['ds_mes_ano_indicador'];
        $data['qt_doc_recebido']      = $qt_doc_recebido;
        $data['qt_doc_fora']          = $qt_doc_fora;

        $data['permissao_digitalizacao'] = $this->get_permissao_digitalizacao();

        $this->load->view('ecrm/protocolo_digitalizacao/relatorio_partial_result', $data);
    }

    function verifica_participante()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['cd_empresa']            = $this->input->post('cd_empresa');
        $args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado');
        $args['seq_dependencia']       = $this->input->post('seq_dependencia');
        
        $this->documento_protocolo_model->verifica_participante($result, $args);
        $data = $result->row_array();
        echo json_encode($data);
    }

    function zip_docs($cd_protocolo = 0)
    {
        $this->load->library('zip');

        $args['cd_protocolo'] = $cd_protocolo;

        if (intval($args['cd_protocolo']) > 0)
        {
            $this->documento_protocolo_model->zip_docs($result, $args);
            $data = $result->result_array();
			$gerencia_origem = trim($data[0]['cd_gerencia_origem'])."_".strtoupper(trim($data[0]['usuario_envio']));
            $ano = '';
            $contador = '';
			
			if(in_array(trim($data[0]['cd_gerencia_origem']), array("GC","GJ")))
			{
				#### CRIA DIRETORIO TEMP PARA USAR O NOME ORIGINAL ####
				$dir_tmp = "../cieprev/up/protocolo_digitalizacao_tmp_".intval($args['cd_protocolo']);
				if(!is_dir($dir_tmp))
				{
					mkdir($dir_tmp);
				}				

				$dir = "../cieprev/up/protocolo_digitalizacao_".intval($args['cd_protocolo']);
				foreach ($data as $ar_item)
				{
                    $ano = $ar_item['ano'];
                    $contador = $ar_item['contador'];

					$ar_nome  = explode(".",$ar_item['arquivo_nome']);
					$ext      = $ar_nome[ (count($ar_nome) - 1) ];
					$n_ext    = strlen($ar_item['arquivo_nome']) - (strlen($ext) + 1);
					$nome_ori = substr($ar_item['arquivo_nome'], 0, $n_ext)."_".$ar_item['cd_documento_protocolo_item'].".".$ext;
					
					copy($dir."/".$ar_item['arquivo'], $dir_tmp."/".$nome_ori);
					
					$this->zip->read_file($dir_tmp."/".$nome_ori);
					@unlink($dir_tmp."/".$nome_ori);
				}
				
				if(is_dir($dir_tmp))
				{
					@rmdir($dir_tmp);
				}				
			}
			else
			{
				$dir = "../cieprev/up/protocolo_digitalizacao_" . intval($args['cd_protocolo']);
				foreach ($data as $ar_item)
				{
                    $ano = $ar_item['ano'];
                    $contador = $ar_item['contador'];

					$this->zip->read_file($dir . "/" .$ar_item['arquivo']);
				}
			}
            
			$this->zip->download($ano."_".$contador."_".$gerencia_origem."_".$cd_protocolo.".zip");
        }
    }
    
    function receber($cd_documento_protocolo)
    {
        if (gerencia_in(array('GFC')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;
            $data['cd_documento_protocolo'] = $cd_documento_protocolo;
          
            $this->documento_protocolo_model->protocolo_ja_confirmado($result, $args);
            $total = $result->row_array();
            
            if($total['quantos'] > 0)
            {
                exibir_mensagem('Protocolo já confirmado.');
            }
            else
            {
                $this->load->view('ecrm/protocolo_digitalizacao/receber', $data);
            }
        }
        else
        {
            exibir_mensagem('Para receber um documento, você deve ser da Gerência Administrativa.');
        }
    }
    
    function lista_documento_receber($cd_documento_protocolo)
    {
        if (gerencia_in(array('GFC')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;
            
            $this->documento_protocolo_model->carrega_documento_protocolo($result, $args);
            $data['row'] = $result->row_array();
            
            $this->documento_protocolo_model->lista_documento_receber($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->documento_protocolo_model->total_indexado($result, $args);
            $data['total_indexados'] = $result->row_array();
            
            $this->documento_protocolo_model->total_devolvidos($result, $args);
            $data['total_devolvidos'] = $result->row_array();

            $this->load->view('ecrm/protocolo_digitalizacao/receber_result', $data);
        }
        else
        {
            exibir_mensagem('Para receber um documento, você deve ser da Gerência Administrativa.');
        } 
    }
    
    function total_indexados_data()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['dt_indexacao'] = $this->input->post('dt_indexacao', true); 
        
        $this->documento_protocolo_model->total_indexados_data($result, $args);
        $total = $result->row_array();
        
        echo $total['quantos'];
    }
    
    function salvar_receber()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo', true); 
        $args['cd_usuario']             = usuario_id();
        $return                         = $this->input->post('return', true);
        
        while( list($key, $value) = each($_POST) )
	   {
            if( strpos($key, "cd_documento_protocolo_item")>-1 )
            {
                $args['cd_documento_protocolo_item'] = $value;
                $comando = '';

                if(isset($_POST["marcar_check_".$args['cd_documento_protocolo_item']]))
                {
                    $comando = $_POST["marcar_check_".$args['cd_documento_protocolo_item']];
                }
                
                $args['fl_recebido']  = "";
                $args['observacao']   = "";
                $args['dt_devolucao'] = "";
                $args['motivo']       = "";
                $args['dt_indexacao'] = "";
                
                if($comando == "receber")
                {
                    $args['fl_recebido']  = "S";
                    $args['observacao']   = $_POST["observacao_text_".$args['cd_documento_protocolo_item']];
                    $args['dt_indexacao'] = $_POST["dt_indexacao_".$args['cd_documento_protocolo_item']];
                }
                elseif($comando == "devolver")
                {
                    $args['fl_recebido']  = "N";
                    $args['dt_devolucao'] = date("d/m/Y");
                    $args['motivo']       = $_POST["observacao_text_".$args['cd_documento_protocolo_item']];
                }
                
                $this->documento_protocolo_model->salva_documento_receber($result, $args);
            }
        }

        if($return == 1)
        {
            $this->documento_protocolo_model->confirma_documento_receber($result, $args);
            
            redirect('ecrm/protocolo_digitalizacao', 'refresh');
        }
        else if($return == 0)
        {
            redirect('ecrm/protocolo_digitalizacao/receber/'.$args['cd_documento_protocolo'], 'refresh');
        }
    }
    
    function lista_documento_indexar($cd_documento_protocolo)
    {
        if (gerencia_in(array('GFC')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;
            
            $this->documento_protocolo_model->carrega_documento_protocolo($result, $args);
            $data['row'] = $result->row_array();
            
            $this->documento_protocolo_model->lista_documento_indexar($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->documento_protocolo_model->total_indexado($result, $args);
            $data['total_indexados'] = $result->row_array();
            
            $this->documento_protocolo_model->total_devolvidos($result, $args);
            $data['total_devolvidos'] = $result->row_array();
            
            $this->load->view('ecrm/protocolo_digitalizacao/indexar_result', $data);
        }
        else
        {
            exibir_mensagem('Para receber um documento, você deve ser da Gerência Administrativa.');
        }
    }
    
    function indexar($cd_documento_protocolo)
    {
        if (gerencia_in(array('GFC')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;
            $data['cd_documento_protocolo'] = $cd_documento_protocolo;
          
            $this->documento_protocolo_model->protocolo_ja_confirmado($result, $args);
            $total = $result->row_array();
            
            if($total['quantos'] > 0)
            {
                exibir_mensagem('Protocolo já confirmado.');
            }
            else
            {
                $this->load->view('ecrm/protocolo_digitalizacao/indexar', $data);
            }
        }
        else
        {
            exibir_mensagem('Para receber um documento, você deve ser da Gerência Administrativa.');
        }
    }
    
    function salvar_indexar()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['cd_documento_protocolo'] = $this->input->post('cd_documento_protocolo', true); 
        $args['cd_usuario']             = usuario_id();
        $return                         = $this->input->post('return', true);
        
        while( list($key, $value) = each($_POST) )
        {
            if( strpos($key, "cd_documento_protocolo_item")>-1 )
            {
				$args['cd_documento_protocolo_item'] = $value;
				$args['dt_indexacao']                = $_POST["dt_indexacao_".$args['cd_documento_protocolo_item']];
				$args['ds_observacao']               = $_POST["observacao_text_".$args['cd_documento_protocolo_item']];
				$args['fl_recebido']                 = 'S';
				
				$this->documento_protocolo_model->salva_documento_indexar($result, $args);
            }
        }
        
        if($return == 1)
        {
            $this->documento_protocolo_model->confirma_documento_indexar($result, $args);
            
            redirect('ecrm/protocolo_digitalizacao', 'refresh');
        }
        else if($return == 0)
        {
            redirect('ecrm/protocolo_digitalizacao/indexar/'.$args['cd_documento_protocolo'], 'refresh');
        }
    }
	
	function devolver_protocolo($cd_documento_protocolo)
	{
		$args = array();
        $data = array();
        $result = null;
		
		$args['cd_documento_protocolo'] = $cd_documento_protocolo; 
        $args['cd_usuario']             = $this->session->userdata('codigo');
		
		$this->documento_protocolo_model->devolver_protocolo($result, $args);
		
		redirect('ecrm/protocolo_digitalizacao', 'refresh');
		
	}
	
	function financeiro_resgate_protocolo()
	{
		$args = array();
        $data = array();
        $result = null;
		
		$args["dt_resgate_ini"] = trim($this->input->post("dt_resgate_ini", true));
		$args["dt_resgate_fim"] = trim($this->input->post("dt_resgate_fim", true));
		
		$this->documento_protocolo_model->financeiro_resgate_protocolo($result, $args);
        $data['collection'] = $result->result_array();

		#echo "<PRE>"; print_r($data['collection']);
		
        $this->load->view('ecrm/protocolo_digitalizacao/detalhe_financeiro_resgate_result', $data);  
	}	

	public function get_tempo_descarte($cd_documento = 0)
	{
        $cd_tipo_doc = (intval($cd_documento) > 0 ? intval($cd_documento) : $this->input->post("cd_tipo_doc", true));
        $args 		 = array();
        $url 		 = 'http://10.63.255.218:8080/ords/ordsws/tabela_temporalidade_doc/tempo_descarte/index';

        $args = array(
            'id'          => '8dcfac716cf69a12255d63a4abf8b485',
            'cd_tipo_doc' => $cd_tipo_doc
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

        if(intval($cd_documento) > 0)
        {
        	$vl_arquivo_central = (trim($json['result'][0]['vl_arquivo_central']) != '' ? trim($json['result'][0]['vl_arquivo_central']).' - ' : '');

			$ds_tempo_descarte = $vl_arquivo_central . $json['result'][0]['id_arquivo_central'];
			$id_classificacao_info_doc  = $json['result'][0]['id_classificacao_info_doc'];

	        return array('ds_tempo_descarte' => $ds_tempo_descarte, 'id_classificacao_info_doc' => $id_classificacao_info_doc);
        }
        else
        {
        	echo json_encode($json);
        }
	}

    public function editar_indexado($cd_documento_protocolo)
    {
        if($this->get_permissao_digitalizacao())
        {
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;

            $this->documento_protocolo_model->carregar($result, $args);
            $data['row'] = $result->row_array();

            $data['collection'] = $this->documento_protocolo_model->listar_documento_indexado($cd_documento_protocolo);

            $this->load->view('ecrm/protocolo_digitalizacao/editar_indexado', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_indexado($cd_documento_protocolo)
    {
        if($this->get_permissao_digitalizacao())
        {
            $args['cd_documento_protocolo'] = $cd_documento_protocolo;
            $args['cd_usuario']             = $this->session->userdata('codigo');
        
            $this->documento_protocolo_model->excluir_protocolo($result, $args);
        
            redirect('ecrm/protocolo_digitalizacao/editar_indexado/'.intval($cd_documento_protocolo), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_item_indexado($cd_documento_protocolo, $cd_documento_protocolo_item)
    {
        if($this->get_permissao_digitalizacao())
        {
            $args['cd_documento_protocolo_item'] = $cd_documento_protocolo_item;
            $args['cd_usuario_exclusao']         = $this->session->userdata('codigo');
        
            $this->documento_protocolo_model->excluir_documento($result, $args);
        
            redirect('ecrm/protocolo_digitalizacao/editar_indexado/'.intval($cd_documento_protocolo), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function alterar_observacao_indexacao()
    {
        if($this->get_permissao_digitalizacao())
        {
            $cd_documento_protocolo_item = intval($this->input->post('cd_documento_protocolo_item', true));
            $ds_observacao_indexacao     = $this->input->post('ds_observacao_indexacao', true);
            $cd_usuario                  = $this->session->userdata('codigo');
        
            $this->documento_protocolo_model->alterar_observacao_indexacao($cd_documento_protocolo_item, $ds_observacao_indexacao, $cd_usuario);
        }
    }
} 
?>