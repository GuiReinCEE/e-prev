<?php
class parecer_enquadramento_cci extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('gestao/parecer_enquadramento_cci_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GIN', 'GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->parecer_enquadramento_cci_model->usuario_cadastro($result, $args);
			$data['arr_usuario'] = $result->result_array();
					
			$this->load->view('gestao/parecer_enquadramento_cci/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {		
		if(gerencia_in(array('GIN', 'GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args["nr_ano"]              = $this->input->post("nr_ano", TRUE);
			$args["nr_numero"]           = $this->input->post("nr_numero", TRUE);
			$args["fl_situacao"]         = $this->input->post("fl_situacao", TRUE);
			$args["fl_cancelar"]         = $this->input->post("fl_cancelar", TRUE);
			$args["cd_usuario_inclusao"] = $this->input->post("cd_usuario_inclusao", TRUE);
			$args["dt_inclusao_ini"]     = $this->input->post("dt_inclusao_ini", TRUE);
			$args["dt_inclusao_fim"]     = $this->input->post("dt_inclusao_fim", TRUE);
			$args["dt_envio_ini"]        = $this->input->post("dt_envio_ini", TRUE);
			$args["dt_envio_fim"]        = $this->input->post("dt_envio_fim", TRUE);
			$args["dt_limite_ini"]       = $this->input->post("dt_limite_ini", TRUE);
			$args["dt_limite_fim"]       = $this->input->post("dt_limite_fim", TRUE);
			$args["dt_encerrado_ini"]    = $this->input->post("dt_encerrado_ini", TRUE);
			$args["dt_encerrado_fim"]    = $this->input->post("dt_encerrado_fim", TRUE);
			$args["descricao"]           = $this->input->post("descricao", TRUE);
			
			manter_filtros($args);
			
			$this->parecer_enquadramento_cci_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('gestao/parecer_enquadramento_cci/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function cadastro($cd_parecer_enquadramento_cci = 0)
	{
		if(gerencia_in(array('GIN', 'GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $cd_parecer_enquadramento_cci;
			
			if(intval($args["cd_parecer_enquadramento_cci"]) == 0)
			{
				$data['row'] = array(
					'cd_parecer_enquadramento_cci' => intval($cd_parecer_enquadramento_cci),
					'descricao'                    => '',
					'nr_ano_numero'                => '',
					'dt_limite'                    => date('d/m/Y',mktime(0,0,0,date('m'),date('d')+10,date('Y'))),
					'dt_envio'                     => '',
					'dt_inclusao'                  => '',
					'dt_encerrado'                 => '',
					'usuario_cadastro'             => '',
					'cd_usuario_envio'             => '',
					'usuario_encerrado'            => ''
				);
			}
			else
			{
				$this->parecer_enquadramento_cci_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/parecer_enquadramento_cci/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar()
	{
		if(gerencia_in(array('GIN', 'GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $this->input->post("cd_parecer_enquadramento_cci", TRUE);
			$args["descricao"]                    = $this->input->post("descricao", TRUE);
			$args["dt_limite"]                    = $this->input->post("dt_limite", TRUE);
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$args['cd_parecer_enquadramento_cci'] = $this->parecer_enquadramento_cci_model->salvar($result, $args);
			
			redirect("gestao/parecer_enquadramento_cci/cadastro/".$args["cd_parecer_enquadramento_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function salvar_prorrogacao()
	{
		if(gerencia_in(array('GIN', 'GC')))
		{
			$cd_parecer_enquadramento_cci = $this->input->post('cd_parecer_enquadramento_cci', TRUE);
			$dt_limite_prorrogacao        = $this->input->post('dt_limite_prorrogacao', TRUE);
			
			$this->parecer_enquadramento_cci_model->salvar_prorrogacao($cd_parecer_enquadramento_cci, $dt_limite_prorrogacao, $this->session->userdata('codigo'));
			
			redirect('gestao/parecer_enquadramento_cci/cadastro/'.$cd_parecer_enquadramento_cci, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	function enviar($cd_parecer_enquadramento_cci)
	{
		if(gerencia_in(array('GC', 'GIN')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $cd_parecer_enquadramento_cci;
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$this->parecer_enquadramento_cci_model->enviar($result, $args);
			
			redirect("gestao/parecer_enquadramento_cci/cadastro/".$args["cd_parecer_enquadramento_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function encerrar($cd_parecer_enquadramento_cci)
	{
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $cd_parecer_enquadramento_cci;
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$this->parecer_enquadramento_cci_model->encerrar($result, $args);
			
			redirect("gestao/parecer_enquadramento_cci/cadastro/".$args["cd_parecer_enquadramento_cci"], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function anexo($cd_parecer_enquadramento_cci)
	{	
		if(gerencia_in(array('GC', 'GIN')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $cd_parecer_enquadramento_cci;
			
			$this->parecer_enquadramento_cci_model->carrega($result, $args);
			$row = $result->row_array();
			
			$data['row'] = $row;
			
			$this->load->view('gestao/parecer_enquadramento_cci/anexo', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function listar_anexo()
	{
		if(gerencia_in(array('GC', 'GIN')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci"] = $this->input->post("cd_parecer_enquadramento_cci", TRUE);
			
			$this->parecer_enquadramento_cci_model->carrega($result, $args);
			$row = $result->row_array();
			
			$data['fl_salvar'] = false;
			
			if(trim($row['dt_envio']) == '')
			{
				$data['fl_salvar'] = true;
			}
			elseif((trim($row['dt_envio']) != '') AND (trim($row['dt_encerrado']) == '') AND (trim($this->session->userdata('divisao')) == 'GC'))
			{
				$data['fl_salvar'] = true;
			}
			
			$data['row'] = $row;
			
			$this->parecer_enquadramento_cci_model->listar_anexo($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('gestao/parecer_enquadramento_cci/anexo_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar_anexo()
	{
		if(gerencia_in(array('GC', 'GIN')))
		{
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
					
					$args['cd_parecer_enquadramento_cci'] = $this->input->post("cd_parecer_enquadramento_cci", TRUE);
					$args["cd_usuario"]                   = $this->session->userdata('codigo');					
					
					$this->parecer_enquadramento_cci_model->salvar_anexo($result, $args);
					
					$nr_conta++;
				}
			}			
			
			redirect("gestao/parecer_enquadramento_cci/anexo/".intval($args["cd_parecer_enquadramento_cci"]), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir_anexo($cd_parecer_enquadramento_cci, $cd_parecer_enquadramento_cci_anexo)
	{
		if(gerencia_in(array('GC')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_parecer_enquadramento_cci_anexo"] = $cd_parecer_enquadramento_cci_anexo;
			$args["cd_usuario"]                         = $this->session->userdata('codigo');
			
			$this->parecer_enquadramento_cci_model->excluir_anexo($result, $args);
			
			redirect("gestao/parecer_enquadramento_cci/anexo/".intval($cd_parecer_enquadramento_cci), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	public function cancelar($cd_parecer_enquadramento_cci)
	{
		if(gerencia_in(array('GC', 'GIN')))
		{
			$data['row'] = array(
				'cd_parecer_enquadramento_cci'   => intval($cd_parecer_enquadramento_cci),
				'dt_cancelamento'                => '',
				'cd_usuario_cancelamento'        => '',
				'ds_justificativa_cancelamento'  => ''
			);

			$args['cd_parecer_enquadramento_cci'] = $cd_parecer_enquadramento_cci;

			$this->parecer_enquadramento_cci_model->carrega($result, $args);
			$data['row'] = $result->row_array();

			$this->load->view('gestao/parecer_enquadramento_cci/cancelar', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	public function salvar_cancelamento()
	{
		if(gerencia_in(array('GC', 'GIN')))
		{
			$args = array(
				'cd_parecer_enquadramento_cci'   => $this->input->post("cd_parecer_enquadramento_cci", TRUE),
				'ds_justificativa_cancelamento'  => $this->input->post("ds_justificativa_cancelamento", TRUE),
				'cd_usuario'                     => $this->session->userdata('codigo')
			);

			$this->parecer_enquadramento_cci_model->salva_cancelamento($args);
			
			redirect('gestao/parecer_enquadramento_cci/index/', 'refresh');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>