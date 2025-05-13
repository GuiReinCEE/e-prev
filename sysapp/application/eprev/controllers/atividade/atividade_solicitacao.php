<?php
class Atividade_solicitacao extends Controller
{
	var $ar_gerencia_atividade = Array();
	
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/atividade_solicitacao_model');

		$this->ar_gerencia_atividade[] = array('value' => 'GAD', 'text' => 'Adm - Serviços Gerais');
		$this->ar_gerencia_atividade[] = array('value' => 'GAP', 'text' => 'Atendimento');
		$this->ar_gerencia_atividade[] = array('value' => 'GFC-DIG', 'text'  => 'Arquivo');		
		$this->ar_gerencia_atividade[] = array('value' => 'GCM-CAD', 'text' => 'Cadastro');
		$this->ar_gerencia_atividade[] = array('value' => 'GRI', 'text' => 'Comunicação');
		$this->ar_gerencia_atividade[] = array('value' => 'GA', 'text'  => 'Atuarial');
		$this->ar_gerencia_atividade[] = array('value' => 'GB', 'text'  => 'Benefícios');
		$this->ar_gerencia_atividade[] = array('value' => 'GC', 'text'  => 'Controladoria');
		$this->ar_gerencia_atividade[] = array('value' => 'GF', 'text'  => 'Financeiro');
		$this->ar_gerencia_atividade[] = array('value' => 'GJ', 'text'  => 'Jurídico');
		$this->ar_gerencia_atividade[] = array('value' => 'GS-RH', 'text'  => 'Recursos Humanos');
		$this->ar_gerencia_atividade[] = array('value' => 'GI', 'text'  => 'TI');
		$this->ar_gerencia_atividade[] = array('value' => 'GI', 'text'  => 'TI - Suporte');
    }

    private function novas_gerencias($cd_gerencia_destino)
    {
    	switch ($cd_gerencia_destino) 
		{
		    case 'GI':
		        return 'GTI';
		        break;
		    case 'GAD':
		        return 'GTI-ADM';
		        break;
		    case 'GAP':
		        return 'GCM';
		        break;
		    case 'GA':
		        return 'GC';
		        break;
		    case 'GB':
		        return 'GP';
		        break;
		    case 'GRI':
		        return 'GCM';
		        break;
		    case 'GF':
		        return 'GFC';
		        break;   
		    default:
		    	return $cd_gerencia_destino;
		    	break;
		        
		}
    }
    
    public function index($cd_gerencia_destino, $numero = 0, $cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '', $cd_atendimento = '', $forma_atendimento = '')
    {
		$args = array();
        $data = array();
        $result = null;

        if(intval($numero) > 0)
        {
        	//$cd_gerencia_destino = $numero;

        	$args['numero'] = intval($numero);
        	
			$this->atividade_solicitacao_model->getArea($result, $args);
			$ar_area = $result->row_array(); 

			if(trim($ar_area['area']) != trim($cd_gerencia_destino))
			{
				redirect("atividade/atividade_solicitacao/index/".trim($ar_area['area']).'/'.$args['numero'], "refresh");
			}
        }
        
        if((is_numeric($cd_gerencia_destino)))
        {
			$args['numero'] = intval($cd_gerencia_destino);
        	
			$this->atividade_solicitacao_model->getArea($result, $args);
			$ar_area = $result->row_array(); 

			$args['cd_gerencia_destino'] = trim(strtoupper($ar_area['cd_gerencia_destino']));
			
			redirect("atividade/atividade_solicitacao/index/".trim($ar_area['area']).'/'.$args['numero'], "refresh");
        }
        else
        {
        	$args['cd_gerencia'] 		 = trim(strtoupper($cd_gerencia_destino));
        	$args['cd_gerencia_destino'] = trim(strtoupper($cd_gerencia_destino));
        	$args['numero']      		 = intval($numero);
        }

        $args['cd_gerencia_solicitante'] = $this->session->userdata('divisao');

        $this->atividade_solicitacao_model->cb_sistema($result, $args);
		$data['ar_sistema'] = $result->result_array();	

        $this->atividade_solicitacao_model->solicitante($result, $args);
        $data['arr_solicitante'] = $result->result_array();

        $this->atividade_solicitacao_model->tipo_manutencao($result, $args);
        $data['arr_tipo_manutencao'] = $result->result_array();
        
        $this->atividade_solicitacao_model->tipo_atividade($result, $args);
        $data['arr_tipo_atividade'] = $result->result_array();
        
        $this->atividade_solicitacao_model->atendente($result, $args);
        $data['arr_atendente'] = $result->result_array();
		
		$this->atividade_solicitacao_model->plano($result, $args);
		$data['arr_plano'] = $result->result_array();
		
		$this->atividade_solicitacao_model->solicitante_participante($result, $args);
		$data['arr_solicitante_participante'] = $result->result_array();
		
		$this->atividade_solicitacao_model->forma_solicitacao($result, $args);
		$data['arr_forma_solicitacao'] = $result->result_array();
		
	    $this->atividade_solicitacao_model->atividade_filha($result, $args);
        $data['ar_atividade_filha'] = $result->result_array();			

		
		$data['ar_gerencia_abrir_ao_encerrar'] = $this->ar_gerencia_atividade;
		
		$data["row"] = array();

		$args['cd_nova_gerencia'] = $this->novas_gerencias($cd_gerencia_destino);

		$row = $this->atividade_solicitacao_model->gerencia_destino($result, $args);

        if((intval($args['numero']) == 0) AND (trim($args['cd_gerencia']) != ''))
		{   
			$data["fl_salvar"] = true;

			$cod_atendente = '';
			$cd_substituto = '';

			if(trim($cd_gerencia_destino) == 'GAD')
			{
				$cod_atendente = 5;
				$cd_substituto = 359;
			}
			else if(trim($cd_gerencia_destino) == 'GCM-CAD')
			{
				$cod_atendente = 146;
				$cd_substituto = 384;
			}
			else if(trim($cd_gerencia_destino) == 'GFC-DIG')
			{
				$cod_atendente = 79;
				$cd_substituto = 457;
			}

            if(count($row) > 0)
            {
                $data['row'] = array(
                    'numero'                => $args['numero'],
                    'cd_gerencia_destino_nova'   => $args['cd_nova_gerencia'],
                    'cd_gerencia_destino'   => $args['cd_gerencia'],
                    'gerencia_destino'      => $args['cd_nova_gerencia'].' - '.$row['nome'],
                    'dt_cad'                => '',
                    'cod_solicitante'       => $this->session->userdata('codigo'),
                    'tipo_solicitacao'      => '',
                    'tipo'                  => '',
                    'cd_recorrente'         => 'N',
                    'titulo'                => '',
                    'descricao'             => '',
                    'problema'              => '',
                    'cod_atendente'         => $cod_atendente,
                    'cd_substituto'         => $cd_substituto,
                    'dt_limite'             => '',
					'cd_empresa'            => $cd_empresa,
					'cd_plano'              => '',
					'cd_registro_empregado' => $cd_registro_empregado,
					'seq_dependencia'       => $seq_dependencia,
					'nome_participante'     => '',
					'solicitante'           => '',
					'forma'                 => $forma_atendimento,
					'tp_envio'              => '',
					'cd_atendimento'        => $cd_atendimento,
					'qt_anexo'              => 0 ,
					'cod_testador'          => '',
					'status_atual'          => '',
					'dt_fim_real'           => '',
					'fl_abrir_encerrar'             => 'N',
					'cd_gerencia_abrir_ao_encerrar' => '',
					'cd_tipo_solicitacao_abrir_ao_encerrar'     => '',
					'cd_tipo_abrir_ao_encerrar'     => '',
					'cd_usuario_abrir_ao_encerrar'  => 0,
					'descricao_abrir_ao_encerrar'   => '',
					'sistema' 						=> ''
                );
            }
            else
            {
                exibir_mensagem("A ".$args['cd_gerencia']." NÃO PERMITE A ABERTURA DE ATIVIDADE.");
            }
        }
        else if(intval($args['numero']) > 0)
        {
            $this->atividade_solicitacao_model->carrega($result, $args);
			$data['row'] = $result->row_array();

			$data['row']['cd_gerencia_destino_nova'] = $args['cd_nova_gerencia'];

			$args["cd_gerencia"] = $data['row']["cd_gerencia_destino"];

			if((trim($data['row']['dt_fim_real']) == '') AND ((intval($data['row']['cd_substituto']) == $this->session->userdata("codigo")) OR (intval($data['row']['cod_solicitante']) == $this->session->userdata("codigo")) OR (intval($data['row']['cod_atendente']) == $this->session->userdata("codigo"))))
			{
				$data["fl_salvar"] = true;
			}
			else
			{
				$data["fl_salvar"] = false;
			}

			$data['row']['gerencia_destino'] = $args['cd_nova_gerencia'].' - '.$row['nome'];
        }

        if(trim($data['row']['tipo']) == 'L')
        {
        	$this->load->view('atividade/atividade_solicitacao/index_cenario_legal', $data);
        }
        else
        {
	        #echo "aqui".trim($args['cd_gerencia']); exit;
			
			switch (trim($args['cd_gerencia']))
	        {
	            case 'GI' :
	                $this->load->view('atividade/atividade_solicitacao/index_gi', $data);
	                break;
	            case 'GAD' :
	                $this->load->view('atividade/atividade_solicitacao/index_gad', $data);
	                break;					
	            case 'GAP' :
	                $this->load->view('atividade/atividade_solicitacao/index_gap', $data);
	                break;
			    case 'GB' :
	                $this->load->view('atividade/atividade_solicitacao/index_gb', $data);
	                break;
				case 'GF' :
	                $this->load->view('atividade/atividade_solicitacao/index_gf', $data);
	                break;
				case 'GC' :
	                $this->load->view('atividade/atividade_solicitacao/index_gc', $data);
	                break;
			    case 'GA' :
	                $this->load->view('atividade/atividade_solicitacao/index_ga', $data);
	                break;
				case 'GRI' :
	                $this->load->view('atividade/atividade_solicitacao/index_gri', $data);
	                break;
	            case 'GC-RH' :
	                $this->load->view('atividade/atividade_solicitacao/index_grc_rh', $data);
	                break;					
	            case 'GS-RH' :
	                $this->load->view('atividade/atividade_solicitacao/index_grc_rh', $data);
	                break;
	            case 'GRC-RH' :
	                $this->load->view('atividade/atividade_solicitacao/index_grc_rh', $data);
	                break;
	            case 'GFC-DIG' :
	                $this->load->view('atividade/atividade_solicitacao/index_gfc_dig', $data);
	                break;
				case 'GJ' :
	                $this->load->view('atividade/atividade_solicitacao/index_gj', $data);
	                break;
	            case 'GCM-CAD' :
	                $this->load->view('atividade/atividade_solicitacao/index_gcm_cad', $data);
	                break;				
	        }
    	}
    }

    public function get_descricao_projeto()
	{
		$codigo = $this->input->post("codigo", TRUE);

		$projeto = $this->atividade_solicitacao_model->cb_sistema_descricao($codigo);
		
		$projeto['descricao'] = utf8_encode($projeto['descricao']);

        echo json_encode($projeto);		
	}
    
	public function getAtendente()
	{
		$args = array();
        $data = array();
        $result = null;        
		
		$args["numero"]      = $this->input->post("numero", TRUE);
		$args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
		
		$this->atividade_solicitacao_model->atendente($result, $args);
        $ar_atendente = $result->result_array();		

		foreach($ar_atendente as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
        echo json_encode($data);		
	}
	
	public function getTipoManutencao()
	{
		$args = array();
        $data = array();
        $result = null;        
		
		$args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
		
	    $this->atividade_solicitacao_model->tipo_manutencao($result, $args);
        $ar_tipo_manutencao = $result->result_array();	

		foreach($ar_tipo_manutencao as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
        echo json_encode($data);		
	}	
	
	public function getTipoAtividade()
	{
		$args = array();
        $data = array();
        $result = null;        
		
		$args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
		
	    $this->atividade_solicitacao_model->tipo_atividade($result, $args);
        $ar_tipo_atividade = $result->result_array();	

		foreach($ar_tipo_atividade as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
        echo json_encode($data);		
	}

	public function getTipoAtividadeBloombergGFC()
	{
		$args = array();
        $data = array();
        $result = null;        
		
		$args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
		$tipo_solicitacao = $this->input->post("tipo_solicitacao", TRUE);
		
	    $this->atividade_solicitacao_model->tipo_atividade($result, $args);
        $ar_tipo_atividade = $result->result_array();	

		foreach($ar_tipo_atividade as $item)
		{
			if(trim($tipo_solicitacao) == 'BLPA' AND (trim($item['value']) == 'BPAA' OR trim($item['value']) == 'BPAT'))
			{
				$data[] = array(
					'value' => $item['value'],
					'text'  => utf8_encode($item['text'])
				);
			}
			else if (trim($tipo_solicitacao) != 'BLPA' AND trim($item['value']) != 'BPAA' AND trim($item['value']) != 'BPAT')
			{
				$data[] = array(
					'value' => $item['value'],
					'text'  => utf8_encode($item['text'])
				);
			}
			
		}
		
        echo json_encode($data);		
	}
	
    public function salvar()
	{
        $args = Array();
        $data = Array();
        $result = null;
        
        $arr_status["GB"]  = "AISB";
        $arr_status["GF"]  = "AINF";
        $arr_status["GRI"] = "AICS";
        $arr_status["GC-RH"] = "AIRH";
        $arr_status["GFC-DIG"] = "AIDI";
        $arr_status["GA"]  = "AIGA";
        $arr_status["GAD"] = "AIGD";
        $arr_status["GI"]  = "AINI";
        $arr_status["GAP"] = "AIST";
        $arr_status["GCM-CAD"]  = "CAAI";
        $arr_status["GC"]  = "GCAI";
        $arr_status["GJ"]  = "AIGJ";
        $arr_status["SG"]  = "SGAI";
			
        $args['numero']                = $this->input->post("numero", TRUE);
        $args['area']                  = $this->input->post("cd_gerencia_destino", TRUE);
        $args['cod_solicitante']       = $this->input->post("cod_solicitante", TRUE);
        $args['tipo_solicitacao']      = $this->input->post("tipo_solicitacao", TRUE);
        $args['tipo']                  = $this->input->post("tipo", TRUE);
        $args['cd_recorrente']         = $this->input->post("cd_recorrente", TRUE);
        $args['titulo']                = $this->input->post("titulo", TRUE);
        $args['descricao']             = $this->input->post("descricao", TRUE);
        $args['problema']              = $this->input->post("problema", TRUE);
        $args['cod_atendente']         = $this->input->post("cod_atendente", TRUE);
        $args['cd_substituto']         = $this->input->post("cd_substituto", TRUE);
        $args['dt_limite']             = $this->input->post("dt_limite", TRUE);
        $args['status_atual']          = trim($arr_status[strtoupper($this->input->post("cd_gerencia_destino", TRUE))]);
		$args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);
		$args['cd_plano']              = $this->input->post("cd_plano", TRUE);
		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);
		$args['solicitante']           = $this->input->post("solicitante", TRUE);
		$args['forma']                 = $this->input->post("forma", TRUE);
		$args['tp_envio']              = $this->input->post("tp_envio", TRUE);
		$args['cd_atendimento']        = $this->input->post("cd_atendimento", TRUE);
		 
		$args['fl_abrir_encerrar']                     = $this->input->post("fl_abrir_encerrar", TRUE);
		$args['cd_gerencia_abrir_ao_encerrar']         = $this->input->post("cd_gerencia_abrir_ao_encerrar", TRUE);
		$args['cd_tipo_solicitacao_abrir_ao_encerrar'] = $this->input->post("cd_tipo_solicitacao_abrir_ao_encerrar", TRUE);
		$args['cd_tipo_abrir_ao_encerrar']             = $this->input->post("cd_tipo_abrir_ao_encerrar", TRUE);
		$args['cd_usuario_abrir_ao_encerrar']          = $this->input->post("cd_usuario_abrir_ao_encerrar", TRUE);
		$args['descricao_abrir_ao_encerrar']           = $this->input->post("descricao_abrir_ao_encerrar", TRUE);
		$args['sistema']          					   = $this->input->post("sistema", TRUE);
		
        $args['cd_usuario']            = $this->session->userdata("codigo");

        $numero = $this->atividade_solicitacao_model->salvar($result, $args);
		$area   = $args['area'];
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				$args['cd_atividade']  = $numero;
				$args["cd_usuario"]    = $this->session->userdata('codigo');
				
				$this->atividade_solicitacao_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		

        redirect("atividade/atividade_solicitacao/index/".trim($area).'/'.intval($numero), "refresh");

	}
    
    public function proximo_dia_util()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['tipo_solicitacao'] = $this->input->post("tipo_solicitacao", TRUE);		
		$args['dt_data']          = $this->input->post("dt_data", TRUE);
		
		if(trim($args['dt_data']) == '')
		{
			$args['dt_data'] = date("d/m/Y");
		}
		
		$fl_dia_util = true;
		
		if(trim($args['tipo_solicitacao']) != "")
		{
			$this->atividade_solicitacao_model->data_limite($result, $args);
			$row = $result->row_array();
			
			if(intval($row['qt_dias']) > 0)
			{
				$args['qt_dias'] = intval($row['qt_dias']);
				
				if(trim($row['dt_data_limite']) != "")
				{
					$args['dt_data'] = $row['dt_data_limite'];
				}
				
				if(trim($row['fl_dia_util']) == "N")
				{
					$fl_dia_util = false;
				}
			}
			else
			{
				echo "";
				exit;
			}
		}
		
		if(trim($args['qt_dias']) == "")
		{
			$args['qt_dias'] = 1;
		}
		
		$args['fl_dia_util'] = $fl_dia_util;
		
		
		$this->atividade_solicitacao_model->proximo_dia_util($result, $args);
		$row = $result->row_array();
		
		echo json_encode($row);
    }

    public function proximo_dia_util_cadastro()
    {
    	$tipo_solicitacao = $this->input->post("tipo_solicitacao", TRUE);		
    	$tipo             = $this->input->post("tipo", TRUE);		
		$dt_data          = $this->input->post("dt_data", TRUE);
		
		if(trim($dt_data) == '')
		{
			$dt_data = date("d/m/Y");
		}

		if(trim($tipo_solicitacao) == 'CADG')
		{
			$dias = 10;
		}
		else if(trim($tipo_solicitacao) == 'CADJ')
		{
			$dias = 30;
		}
		else if(trim($tipo_solicitacao) == 'CADI')
		{
			$dias = 30;

			if(trim($tipo) == 'CADU')
			{
				$dias = 5;
			}
		}
		else if(trim($tipo_solicitacao) == 'CADC')
		{
			$dias = 30;

			if(trim($tipo) == 'CADU')
			{
				$dias = 5;
			}
		}
		else if(trim($tipo_solicitacao) == 'CADR')
		{
			$dias = 30;

			if(trim($tipo) == 'CADU')
			{
				$dias = 5;
			}
		}
		else if(trim($tipo_solicitacao) == 'CADO')
		{
			$dias = 30;

			if(trim($tipo) == 'CADU')
			{
				$dias = 5;
			}
		}

		$result = null;
		$args['fl_dia_util'] = TRUE;
		$args['dt_data']     = $dt_data;
		$args['qt_dias']     = $dias;
		
		$this->atividade_solicitacao_model->proximo_dia_util($result, $args);
		$row = $result->row_array();
		
		echo json_encode($row);
    }
	
	public function descricao_atividade()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['tipo_solicitacao'] = $this->input->post("tipo_solicitacao", TRUE);	
        $args['cd_gerencia']      = $this->input->post("cd_gerencia", TRUE);	
		
		$this->atividade_solicitacao_model->descricao_atividade($result, $args);
		$row = $result->row_array();
		
		echo json_encode(array('obs' => utf8_encode($row['obs'])));
	}
	
	public function gap_atendimento($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '', $forma_atendimento = '', $cd_atendimento = '')
	{
		$args = Array();
        $data = Array();
        $result = null;

        switch ($forma_atendimento) 
        {
		    case 'T':
		        $forma_atendimento = 'FAP3';
		        break;
		    case 'P':
		        $forma_atendimento = 'FAP4';
		        break;
		    case 'C':
		        $forma_atendimento = 'FAP5';
		        break;
		    case 'E':
		        $forma_atendimento = 'FAP2';
		        break;
		    case 'A':
		        $forma_atendimento = 'FAP6';
		        break;				
		}
		
		$data['cd_empresa']            = $cd_empresa;
		$data['cd_registro_empregado'] = $cd_registro_empregado;
		$data['seq_dependencia']       = $seq_dependencia;
		$data['forma_atendimento']     = $forma_atendimento;
		$data['cd_atendimento']        = $cd_atendimento;
		$data['ar_gerencia_abrir_ao_encerrar'] = $this->ar_gerencia_atividade;
				
		$this->load->view('atividade/atividade_solicitacao/gap_atendimento', $data);
	}
	
	public function reabrir_atividade($numero, $cd_gerencia_destino)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['numero']      = $numero;	
        $args['cd_gerencia'] = $cd_gerencia_destino;	
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$numero = $this->atividade_solicitacao_model->reabrir_atividade($result, $args);
		
		redirect("atividade/atividade_solicitacao/index/".trim($cd_gerencia_destino).'/'.intval($numero), "refresh");
	}
  
    public function concluirAtividade()
	{
        $args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_usuario']            = $this->session->userdata("codigo");		
        $args['cd_atividade']          = $this->input->post("numero", TRUE);
        $args['cd_gerencia_destino']   = $this->input->post("cd_gerencia_destino", TRUE);
        $args['complemento_conclusao'] = $this->input->post("complemento_conclusao", TRUE);
        $args['fl_concluir']           = $this->input->post("fl_concluir", TRUE);

		$this->atividade_solicitacao_model->concluirAtividade($result, $args);

        redirect("atividade/atividade_solicitacao/index/".trim($args['cd_gerencia_destino']).'/'.intval($args['cd_atividade']), "refresh");
	} 

	public function imprimir($numero, $cd_gerencia_destino)
	{
		$args = array(
			'numero' => $numero
		);

		$this->load->model('projetos/atividade_historico_model');
		$this->load->model('projetos/atividade_acompanhamento_model');

		$this->atividade_solicitacao_model->carrega($result, $args);
		$row = $result->row_array();

		$cd_nova_gerencia = $this->novas_gerencias($row['cd_gerencia_destino']);

		$this->load->plugin('fpdf');
				
		$ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');				
		$ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Atividade - ".$cd_nova_gerencia;

        $ob_pdf->AddPage();

        $ob_pdf->SetFont('segoeuib', '', 12);

        if(trim($row['status_atividade']) == 'Em testes')
        {
        	$ob_pdf->SetTextColor(220,50,50);#vermelho
        }
        else if(trim($row['status_atividade']) == 'Aguardando início')
        {
			$ob_pdf->SetTextColor(0,127,14);#verde
        }
        else if(trim($row['status_atividade']) == 'Aguardando usuário')
        {
			$ob_pdf->SetTextColor(255,140,0);#laranja
        }
        else
        {
			$ob_pdf->SetTextColor(63,72,204);#azul
        }		
		
		$ob_pdf->MultiCell(190, 5.5, '#'.$row['numero'].' - '.$row['titulo'], 0, 'L');
        $ob_pdf->SetTextColor(0,0,0);
        $ob_pdf->MultiCell(190, 5.5, '-------------------------------------------------------------------------------------------------------------', 0, 'L');
        $ob_pdf->SetFont('segoeuib', '', 10);

        if(trim($row['data_conclusao']) != '')
        {
        	$ob_pdf->MultiCell(190, 5.5, 'Dt. Solicitação:        |  Dt. Conclusão:', 0, 'L');
        	$ob_pdf->SetFont('segoeuil', '', 10);		
        	$ob_pdf->MultiCell(190, 5.5, $row['dt_cad'].'  |  '.$row['data_conclusao'], 0, 'L');
        }
        else
        {
        	$ob_pdf->MultiCell(190, 5.5, 'Dt. Solicitação: ', 0, 'L');
        	$ob_pdf->SetFont('segoeuil', '', 10);		
        	$ob_pdf->MultiCell(190, 5.5, $row['dt_cad'], 0, 'L');
        } 

        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->MultiCell(190, 5.5, 'Prioridade: ', 0, 'L');
    	$ob_pdf->SetFont('segoeuil', '', 10);		
    	$ob_pdf->MultiCell(190, 5.5, $row['nr_prioridade'], 0, 'L');     

		$ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->MultiCell(190, 5.5, 'Status: ', 0, 'L');
        $ob_pdf->SetFont('segoeuil', '', 10);
        $ob_pdf->MultiCell(190, 5.5, $row['status_atividade'], 0, 'L');

        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->MultiCell(190, 5.5, 'Solicitante: ', 0, 'L');   
        $ob_pdf->SetFont('segoeuil', '', 10);
        $ob_pdf->MultiCell(190, 5.5, $row['ds_solicitante'], 0, 'L'); 

        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->MultiCell(190, 5.5, 'Atendente: ', 0, 'L');
        $ob_pdf->SetFont('segoeuil', '', 10);
        $ob_pdf->MultiCell(190, 5.5, $row['ds_atendente'], 0, 'L');

        if($row['ds_substituto'] != '')
        {
        	$ob_pdf->SetFont('segoeuib', '', 10);
	       	$ob_pdf->MultiCell(190, 5.5, 'Atendente Substituto: ', 0, 'L'); 
	       	$ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, $row['ds_substituto'], 0, 'L');
        }           
             
        $ob_pdf->SetFont('segoeuib', '', 10);
        $ob_pdf->MultiCell(190, 5.5, 'Descrição: ', 0, 'L');        

        $ob_pdf->SetFont('segoeuil', '', 10);
        $ob_pdf->MultiCell(190, 5.5, $row['descricao'] , 0, 'L');

        if( $row['problema'] != '')
        {
	        $ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, 'Justificativa: ', 0, 'L');        

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, $row['problema'] , 0, 'L');
		}
        $args['cd_atividade'] = $row['numero'];
        $args['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->atividade_historico_model->listar($result, $args);
		$historico = $result->result_array();

		$this->atividade_acompanhamento_model->listar($result, $args);
		$acompanhamento = $result->result_array();

		if(empty($historico) == false)
		{
			$ob_pdf->SetFont('segoeuib', '', 10);
			$ob_pdf->MultiCell(190, 5.5,'Histórico: ', 0, 'L');

			foreach ($historico as $key => $item) 
			{
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, 5.5, '*'.$item['data'].' - '.$item['responsavel'] , 0, 'L');
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, 5.5, $item['status'].': '.$item['complemento'] , 0, 'L');
			}
		}

		if(empty($acompanhamento) == false)
		{
			$ob_pdf->SetFont('segoeuib', '', 10);
			$ob_pdf->MultiCell(190, 5.5,'Acompanhamento: ', 0, 'L');

			foreach ($acompanhamento as $key => $item) 
			{			
				$ob_pdf->SetAligns(array('J', 'C', 'C'));
				$ob_pdf->SetTextColor(0,0,0);

				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, 5.5, '*'.$item['dt_inclusao'].' - '.$item['nome'] , 0, 'L');
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell(190, 5.5, 'Descrição: '.$item['ds_atividade_acompanhamento'] , 0, 'L');
			}
		}

		if(trim($row['solucao']) != '')
		{
			$ob_pdf->SetY($ob_pdf->GetY() + 3);

			$ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, 'Descrição da Manutenção: ', 0, 'L');        

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, $row['solucao'] , 0, 'L');
    	}


		$ob_pdf->SetY($ob_pdf->GetY() + 3);

		$valida_participante = $this->atividade_solicitacao_model->valida_participante($row['cd_empresa'], $row['cd_registro_empregado'], $row['seq_dependencia']);

		if($valida_participante['count'] == 1)
		{
			$ob_pdf->SetFont('segoeuib', '', 10);
	        $ob_pdf->MultiCell(190, 5.5, 'Participante: '.$row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'], 0, 'L');

	    	$ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Nome: '.$row['nome_participante'] , 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Endereço: '.$row['endereco'].", ".$row['nr_endereco']."/".$row['complemento_endereco']." - ".$row['bairro']." - ".$row['cep']." - ".$row['cidade']." - ".$row['uf'] , 0, 'L');

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'E-mail: '.$row['email'].($row['email_profissional'] != '' ? '/'.$row['email_profissional'] : '') , 0, 'L');

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Telefone 1: '.$row['ddd']." - ".$row['telefone'], 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Telefone 2: '.$row['ddd_celular']." - ".$row['celular'], 0, 'L');

	        $this->atividade_solicitacao_model->plano($result, $args);
			$arr_plano = $result->result_array();

			$plano = '';

			foreach ($arr_plano as $key => $item) 
	        {
	        	if($row['cd_plano'] == $item['value'])
		        {
		        	$plano = $item['text'];
		        }
	        } 

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Plano: '.$plano, 0, 'L');

	        $this->atividade_solicitacao_model->solicitante_participante($result, $args);
			$arr_solicitante_participante = $result->result_array();

			$participante = '';

			foreach ($arr_solicitante_participante as $key => $item) 
	        {
	        	if($row['solicitante'] == $item['value'])
		        {
		        	$participante = $item['text'];
		        }
	        } 

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Solicitante: '.$participante, 0, 'L');

	        $this->atividade_solicitacao_model->forma_solicitacao($result, $args);
			$arr_forma_solicitacao = $result->result_array();

			$forma_solicitacao = '';

			foreach ($arr_forma_solicitacao as $key => $item) 
	        {
	        	if($row['forma'] == $item['value'])
		        {
		        	$forma_solicitacao = $item['text'];
		        }
	        }

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Forma de Solicitação: '.$forma_solicitacao, 0, 'L');

	        $tp_envio = array(array('value' => 1, 'text'=> 'Correio'),array('value' => 2, 'text'=> 'Central de Atendimento'), array('value' => 3, 'text'=> 'E-mail'));

	        $tipo_envio = '';

	        foreach ($tp_envio as $key => $item) 
	        {
	        	if($row['tp_envio'] == $item['value'])
		        {
		        	$tipo_envio = $item['text'];
		        }
	        }

	        $ob_pdf->SetFont('segoeuil', '', 10);
	        $ob_pdf->MultiCell(190, 5.5,'Forma de Envio: '.$tipo_envio, 0, 'L');

	        $ob_pdf->MultiCell(190, 5.5, 'Protocolo de Atendimento: '.$row['cd_atendimento'], 0, 'L');
	        $ob_pdf->SetFont('segoeuil', '', 10);
        }

        $ob_pdf->Output();
        exit;		
	}	
}
?>