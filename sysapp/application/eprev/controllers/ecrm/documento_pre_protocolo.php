<?php

class documento_pre_protocolo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/documento_pre_protocolo_model');
    }

    private function get_permissao()
    {
        #Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
	
	public function index()
    {
		if ($this->get_permissao())
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('ecrm/documento_pre_protocolo/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['fl_protocolo']      = $this->input->post("fl_protocolo", TRUE);
		$args['fl_tipo_protocolo'] = $this->input->post("fl_tipo_protocolo", TRUE);

		manter_filtros($args);

		$this->documento_pre_protocolo_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('ecrm/documento_pre_protocolo/index_result', $data);
    }
	
	public function cadastro($cd_documento_pre_protocolo = 0)
	{
		if ($this->get_permissao())
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_documento_pre_protocolo'] = $cd_documento_pre_protocolo;
					
			if(intval($args['cd_documento_pre_protocolo']) == 0)
			{
				$data['row'] = array (
					'cd_documento_pre_protocolo' => intval($args['cd_documento_pre_protocolo']),
					'cd_tipo_doc'                => '',
					'cd_empresa'                 => '',
					'cd_registro_empregado'      => '',
					'seq_dependencia'            => '',
					'ds_observacao'              => '',
					'fl_descartar'               => '',
					'nr_folha'                   => 1,
					'arquivo'                    => '',
					'arquivo_nome'               => '',
					'fl_manter'                  => 'S',
					'nome'                       => ''
				);
			}
			else
			{
				$this->documento_pre_protocolo_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/documento_pre_protocolo/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function salvar()
	{
		if ($this->get_permissao())
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_documento_pre_protocolo'] = $this->input->post("cd_documento_pre_protocolo", TRUE);
			$args['cd_tipo_doc']                = $this->input->post("cd_tipo_doc", TRUE);
			$args['cd_empresa']                 = $this->input->post("cd_empresa", TRUE);
			$args['cd_registro_empregado']      = $this->input->post("cd_registro_empregado", TRUE);
			$args['seq_dependencia']            = $this->input->post("seq_dependencia", TRUE);
			$args['ds_observacao']              = $this->input->post("ds_observacao", TRUE);
			$args['fl_descartar']               = $this->input->post("fl_descartar", TRUE);
			$args['nr_folha']                   = $this->input->post("nr_folha", TRUE);
			$args['arquivo']                    = $this->input->post("arquivo", TRUE);
			$args['arquivo_nome']               = $this->input->post("arquivo_nome", TRUE);
			$args['nome']                       = $this->input->post("nome", TRUE);
			$args['cd_usuario']                 = $this->session->userdata("codigo");
			
			$this->documento_pre_protocolo_model->salvar($result, $args);
			
			$fl_manter = $this->input->post("fl_manter", TRUE);
			
			if(trim($fl_manter) == 'S')
			{
				redirect("ecrm/documento_pre_protocolo/cadastro", "refresh");
			}
			else
			{
				redirect("ecrm/documento_pre_protocolo/", "refresh");
			}
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function descartar()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_tipo_doc'] = intval($this->input->post('cd_tipo_doc'));
        $args['cd_divisao']  = $this->session->userdata('divisao');

        $this->documento_pre_protocolo_model->descartar($result, $args);
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
	
	function excluir()
	{
		$result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_documento_pre_protocolo'] = intval($this->input->post('cd_documento_pre_protocolo'));
        $args['cd_usuario']                 = $this->session->userdata('codigo');

        $this->documento_pre_protocolo_model->excluir($result, $args);
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
	
	function gerar_protocolo_digitalizacao()
	{
		$result = null;
        $data = Array();
        $args = Array();
	
		$args['cd_divisao'] = $this->session->userdata('divisao');
		
		$arr_check = $this->input->post("check");
		
		foreach($arr_check as $item)
		{
			$args['cd_documento_pre_protocolo'] = $item;
			
			$this->documento_pre_protocolo_model->carrega($result, $args);
			$row = $result->row_array();
			
			$ar_documento[] = array(
				'cd_documento_recebido_item' => 0,
				'cd_tipo_doc'                => $row['cd_tipo_doc'],
				'cd_empresa'                 => $row['cd_empresa'],
				'cd_registro_empregado'      => $row['cd_registro_empregado'],
				'seq_dependencia'            => $row['seq_dependencia'],
				'nome'                       => $row['nome'],
				'ds_observacao'              => $row['ds_observacao'],
				'nr_folha'                   => $row['nr_folha'],
				'arquivo'                    => $row['arquivo'],
				'arquivo_nome'               => $row['arquivo_nome'],
				'cd_usuario_cadastro'        => $this->session->userdata('codigo'),
				'cd_documento_pre_protocolo' => $args['cd_documento_pre_protocolo']
			);				
		}
		
		#### GERA PROTOCOLO ####
		if(count($ar_documento) > 0)
		{
			$args = Array();
            $result = null;
			
			$this->load->model('projetos/Documento_protocolo_model');
			
			#### PROTOCOLO DIGITAL CERTIFICADOS ####
			$args['cd_usuario_cadastro'] = $this->session->userdata('codigo');
			$args['cd_gerencia']         = $this->session->userdata('divisao');
			$args['ano']                 = date('Y');
			$args['tipo_protocolo']      = $this->input->post("fl_tipo_protocolo", TRUE);
			$args['fl_contrato']      	 = "";
			$ar_protocolo = Array();
			
			$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);	
			
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
				$prot['ds_processo']                 = "";		
				$prot['cd_documento']                = $ar_reg['cd_tipo_doc'];
				
				$prot['arquivo']      = '';
				$prot['arquivo_nome'] = '';	

				$prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
				$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);
				
				if(trim($args['tipo_protocolo']) == 'D')
				{
					$arq = $ar_reg['cd_empresa']."_".$ar_reg['cd_registro_empregado']."_".$ar_reg['seq_dependencia']."_".$ar_reg['cd_tipo_doc']."_".uniqid(time()).".pdf";
					$dir = "up/protocolo_digitalizacao_".intval($ar_protocolo['cd_documento_protocolo'])."/";

					$prot['arquivo']      = $arq;
					$prot['arquivo_nome'] = $arq;					
					
					#### COPIAR ARQUIVO PARA PASTA ####
					copy("../cieprev/up/documento_pre_documento/".trim($ar_reg['arquivo']), "../cieprev/".$dir.$arq);	
				}
				$this->Documento_protocolo_model->adicionaDocumento($result, $prot);		

				$args['tipo_documento_criado']      = 'PD';
				$args['cd_documento']               = $prot["cd_documento_protocolo"];
				$args['cd_documento_pre_protocolo'] = $ar_reg["cd_documento_pre_protocolo"];
				$args['cd_usuario']                 = $this->session->userdata('codigo');
				
				$this->documento_pre_protocolo_model->gera_protocolo($result, $args);
			}
			
			echo $prot["cd_documento_protocolo"];
		}
	}
	
	function gerar_protocolo_interno()
	{
		$result = null;
        $data = Array();
        $args = Array();
		
		$this->load->model('projetos/documento_recebido_model');
		
		$args['cd_divisao'] = $this->session->userdata('divisao');
		
		$arr_check = $this->input->post("check");
		
		foreach($arr_check as $item)
		{
			$args['cd_documento_pre_protocolo'] = $item;
			
			$this->documento_pre_protocolo_model->carrega($result, $args);
			$row = $result->row_array();
			
			$ar_documento[] = array(
				'cd_documento_recebido_item' => 0,
				'cd_tipo_doc'                => $row['cd_tipo_doc'],
				'cd_empresa'                 => $row['cd_empresa'],
				'cd_registro_empregado'      => $row['cd_registro_empregado'],
				'seq_dependencia'            => $row['seq_dependencia'],
				'nome'                       => $row['nome'],
				'ds_observacao'              => $row['ds_observacao'],
				'nr_folha'                   => $row['nr_folha'],
				'arquivo'                    => $row['arquivo'],
				'arquivo_nome'               => $row['arquivo_nome'],
				'cd_usuario_cadastro'        => $this->session->userdata('codigo'),
				'cd_documento_pre_protocolo' => $args['cd_documento_pre_protocolo']
			);
			
			if((trim($row['arquivo_nome']) != '') AND (trim($row['arquivo']) != ''))
			{			
				#### COPIAR ARQUIVO PARA PASTA ###
				copy("../cieprev/up/documento_pre_documento/".trim($row['arquivo']), "../cieprev/up/documento_recebido/".trim($row['arquivo']));
			}
		}
		
		#### GERA PROTOCOLO ####
		if(count($ar_documento) > 0)
		{
			$args = Array();
            $result = null;
			
			$args['cd_documento_recebido']      = 0;
			$args["cd_documento_recebido_tipo"] = 1; #Central de Atendimento
			$args["cd_usuario_cadastro"]        = $this->session->userdata('codigo');
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);		
			
			if($cd_documento_recebido > 0)
			{
				#### ADICIONA DOCUMENTOS ####				
				foreach($ar_documento as $args)
				{
					$args["cd_documento_recebido"] = $cd_documento_recebido;
					
					$args["cd_usuario"] = $args['cd_usuario_cadastro'];
					$this->documento_recebido_model->adicionar_documento($result, $args);
					
					$args['tipo_documento_criado'] = 'PI';
					$args['cd_documento']          = $args["cd_documento_recebido"];
					
					$this->documento_pre_protocolo_model->gera_protocolo($result, $args);
				}
			}
			
			echo $cd_documento_recebido;
		}

	}
}
?>