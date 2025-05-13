<?php

class calculo_irrf extends Controller
{
	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('escritorio_juridico/calculo_irrf_model');
    }
	
	public function index()
    {
		$result   = null;
		$data     = array();
		$args     = array();
		
		$this->calculo_irrf_model->correspondentes($result, $args);
		$data['arr_correspondente'] = $result->result_array();
		
		$this->calculo_irrf_model->processos($result, $args);
		$data['arr_processo'] = $result->result_array();
		
		$this->load->view('atividade/calculo_irrf/index', $data);
    }
	
	public function listar()
    {
		$result   = null;
		$data     = array();
		$args     = array();
			
		$arr_ano_nr_processo = explode("/",$this->input->post("ano_nr_processo", TRUE));
		
		$args['nr_ano']                         = $this->input->post("nr_ano", TRUE);
		$args['nr_numero']                      = $this->input->post("nr_numero", TRUE);
		$args['cpf']                            = $this->input->post("cpf", TRUE);
		$args['nome']                           = $this->input->post("nome", TRUE);
		$args['cd_calculo_irrf_correspondente'] = $this->input->post("cd_calculo_irrf_correspondente", TRUE);
		$args['nr_processo_ano']                = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[0] : ''); 
		$args['nr_processo']                    = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[1] : ''); 
		$args['dt_pagamento_ini']               = $this->input->post("dt_pagamento_ini", TRUE);
		$args['dt_pagamento_fim']               = $this->input->post("dt_pagamento_fim", TRUE);
		$args['fl_status']                      = $this->input->post("fl_status", TRUE);
		$args['cd_empresa']                     = $this->input->post("cd_empresa", TRUE);
		$args['cd_registro_empregado']          = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']                = $this->input->post("seq_dependencia", TRUE);
		
		$this->calculo_irrf_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('atividade/calculo_irrf/index_result', $data);
    }
	
	public function cadastro($cd_calculo_irrf)
    {
        if (gerencia_in(array('GP')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_calculo_irrf'] = $cd_calculo_irrf;
			
			$this->calculo_irrf_model->correspondentes($result, $args);
			$data['arr_correspondente'] = $result->result_array();
			
			$this->calculo_irrf_model->tipo($result, $args);
			$data['arr_tipo'] = $result->result_array();
			
			$this->calculo_irrf_model->tipo_aplicacao($result, $args);
			$data['arr_tipo_aplicacao'] = $result->result_array();
			
			$this->calculo_irrf_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->calculo_irrf_model->listar_beneficiario($result, $args);
			$array = $result->result_array();
			
			$args["cpf"]                  = $data["row"]["cpf"];
			$args["cd_escritorio_oracle"] = $data["row"]["cd_escritorio_oracle"];
			$processo                     = $data["row"]["ano_nr_processo"];
			$arr_processo                 = explode("/",$processo);
			$args["proc_ano"]             = $arr_processo[0];
			$args["proc_nro"]             = $arr_processo[1];

			$this->calculo_irrf_model->listar_participante_re($result, $args);
			$data["arr_re"] = $result->result_array();
	
			$data["arr_beneficiario"] = array();
			
			foreach($array as $item)
			{
				$data["arr_beneficiario"][] = $item["cpf"];
			}
			
			$this->load->view('atividade/calculo_irrf/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function carrega_processos()
	{
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['cpf'] = $this->input->post("cpf", TRUE);
		
		$this->calculo_irrf_model->processos_cpf($result, $args);
        $arr = $result->result_array();
		
		echo json_encode($arr);
	}
	
	public function salvar()
	{
		if (gerencia_in(array('GP')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$arr_ano_nr_processo = explode("/",$this->input->post("ano_nr_processo", TRUE));
		
			$args['cd_calculo_irrf']                = $this->input->post("cd_calculo_irrf", TRUE);
			$args['cpf']                            = $this->input->post("cpf", TRUE);
			$args['nome']                           = $this->input->post("nome", TRUE);
			$args['nr_processo_ano']                = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[0] : ''); 
			$args['nr_processo']                    = (count($arr_ano_nr_processo) == 2 ? $arr_ano_nr_processo[1] : ''); 
			$args['cd_calculo_irrf_correspondente'] = $this->input->post("cd_calculo_irrf_correspondente", TRUE);
			$args['cd_calculo_irrf_tipo']           = $this->input->post("cd_calculo_irrf_tipo", TRUE);
			$args['dt_pagamento']                   = $this->input->post("dt_pagamento", TRUE);
			$args['vl_bruto_tributavel']            = $this->input->post("vl_bruto_tributavel", TRUE);
			$args['vl_isento_tributacao']           = $this->input->post("vl_isento_tributacao", TRUE);
			$args['vl_contribuicao']                = $this->input->post("vl_contribuicao", TRUE);
			$args['vl_custeio_administrativo']      = $this->input->post("vl_custeio_administrativo", TRUE);
			$args['vl_desconto_pensao_alimenticia'] = $this->input->post("vl_desconto_pensao_alimenticia", TRUE);
			$args['cd_usuario']                     = $this->session->userdata("cd_usuario");
			$args['cd_escritorio']                  = $this->session->userdata("cd_escritorio");

			$this->calculo_irrf_model->salvar($result, $args);
			
			redirect("atividade/calculo_irrf/cadastro/".intval($args['cd_calculo_irrf']), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function autorizar($cd_calculo_irrf)
	{
		$result   = null;
		$data     = array();
        $args     = array();
	
		$args['cd_calculo_irrf'] = $cd_calculo_irrf;
		$args['cd_usuario']      = $this->session->userdata("codigo");
		
		$this->calculo_irrf_model->autorizar($result, $args);

		redirect("atividade/calculo_irrf", "refresh");
	}
	
	public function liberar($cd_calculo_irrf)
	{
		$result   = null;
		$data     = array();
        $args     = array();
	
		$args['cd_calculo_irrf'] = $cd_calculo_irrf;
		$args['cd_usuario']      = $this->session->userdata("codigo");
		
		$this->calculo_irrf_model->liberar($result, $args);

		redirect("atividade/calculo_irrf", "refresh");
	}
	
	function salvar_re($cd_calculo_irrf, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['cd_calculo_irrf']       = $cd_calculo_irrf;
		$args['cd_empresa']            = $cd_empresa;
		$args['cd_registro_empregado'] = $cd_registro_empregado;
		$args['seq_dependencia']       = $seq_dependencia;
		$args['cd_usuario']            = $this->session->userdata("codigo");
		
		$this->calculo_irrf_model->salvar_re($result, $args);

		redirect("atividade/calculo_irrf/cadastro/".$args['cd_calculo_irrf'], "refresh");
	}
	
	public function anexo($cd_calculo_irrf)
    {
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_calculo_irrf'] = $cd_calculo_irrf;
		
		$this->calculo_irrf_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/calculo_irrf/anexo', $data);
    }
	
	public function salvar_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['cd_calculo_irrf'] = $this->input->post("cd_calculo_irrf", TRUE);
		$args['arquivo_nome']    = $this->input->post("arquivo_nome", TRUE);
		$args['arquivo']         = $this->input->post("arquivo", TRUE);
		$args['cd_usuario']      = $this->session->userdata("codigo");
		
		copy("./up/calculo_irrf/".$args["arquivo"], "./../eletroceee/app/up/escritorio_juridico/".$args["arquivo"]);
		
		$this->calculo_irrf_model->salvar_anexo($result, $args);
		
		redirect('atividade/calculo_irrf/anexo/'.$args['cd_calculo_irrf'], 'refresh');
	}
	
	public function listar_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
			
		$args['cd_calculo_irrf'] = $this->input->post("cd_calculo_irrf", TRUE);
		
		$this->calculo_irrf_model->listar_anexo($result, $args);
        $data['collection'] = $result->result_array();

		$this->load->view('atividade/calculo_irrf/anexo_result', $data);
	}
	
	public function excluir_anexo()
	{
		$result   = null;
		$data     = array();
        $args     = array();
			
		$args['cd_calculo_irrf_anexo'] = $this->input->post("cd_calculo_irrf_anexo", TRUE);
		$args['cd_usuario']            = $this->session->userdata("codigo");
		
		$this->calculo_irrf_model->excluir_anexo($result, $args);
	}
	
	public function rejeitar($cd_calculo_irrf)
	{
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_calculo_irrf'] = $cd_calculo_irrf;
		
		$this->calculo_irrf_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/calculo_irrf/rejeitar', $data);
	}
	
	public function salvar_rejeitar()
	{
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_calculo_irrf']           = $this->input->post("cd_calculo_irrf", TRUE);
		$args['ds_calculo_irrf_rejeitado'] = $this->input->post("ds_calculo_irrf_rejeitado", TRUE);
		$args['cd_usuario']                = $this->session->userdata("codigo");
		
		$this->calculo_irrf_model->salvar_rejeitar($result, $args);
		
		redirect('atividade/calculo_irrf', 'refresh');
	}
}
?>