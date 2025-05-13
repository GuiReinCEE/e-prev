<?php

class reclamacao_analise extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/reclamacao_analise_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->reclamacao_analise_model->classificacao($result, $args);
			$data['arr_classificacao'] = $result->result_array();
			
			$this->reclamacao_analise_model->responsavel($result, $args);
			$data['arr_responsavel'] = $result->result_array();

			$this->load->view('ecrm/reclamacao_analise/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['nr_ano'] 						  = $this->input->post("nr_ano", TRUE);   
			$args['nr_numero']                        = $this->input->post("nr_numero", TRUE);   
			$args['cd_reclamacao_analise_classifica'] = $this->input->post("cd_reclamacao_analise_classifica", TRUE);   
			$args['dt_envio_ini'] 					  = $this->input->post("dt_envio_ini", TRUE);   
			$args['dt_envio_fim'] 					  = $this->input->post("dt_envio_fim", TRUE);   
			$args['cd_usuario_responsavel'] 		  = $this->input->post("cd_usuario_responsavel", TRUE);   
			$args['dt_limite_ini'] 					  = $this->input->post("dt_limite_ini", TRUE);   
			$args['dt_limite_fim'] 					  = $this->input->post("dt_limite_fim", TRUE);   
			$args['dt_prorrogacao_ini'] 			  = $this->input->post("dt_prorrogacao_ini", TRUE);   
			$args['dt_prorrogacao_fim'] 			  = $this->input->post("dt_prorrogacao_fim", TRUE);   
			$args['dt_retorno_ini'] 				  = $this->input->post("dt_retorno_ini", TRUE);   
			$args['dt_retorno_fim'] 				  = $this->input->post("dt_retorno_fim", TRUE);   
			$args['fl_retornado'] 			    	  = $this->input->post("fl_retornado", TRUE);   
			$args['fl_atrasado'] 		  		      = $this->input->post("fl_atrasado", TRUE);   
			
			manter_filtros($args);

			$this->reclamacao_analise_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/reclamacao_analise/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro($cd_reclamacao_analise = 0)
    {
        if (gerencia_in(array('GAP')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_reclamacao_analise'] = intval($cd_reclamacao_analise);
			
			$this->reclamacao_analise_model->classificacao($result, $args);
			$data['arr_classificacao'] = $result->result_array();
			
			$this->reclamacao_analise_model->data_limite($result, $args);
			$row = $result->row_array();
			
            if ($cd_reclamacao_analise == 0)
            {
                $data['row'] = Array(
                  'cd_reclamacao_analise'            => $args['cd_reclamacao_analise'],
				  'cd_reclamacao_analise_classifica' => '',
				  'cd_usuario_responsavel_gerencia'  => '',
				  'cd_usuario_responsavel'           => '',
				  'cd_usuario_substituto_gerencia'   => '',
				  'cd_usuario_substituto'            => '',
				  'dt_limite'                        => $row['dt_limite'],
				  'dt_prorrogacao'                   => '',
				  'observacao'                       => '',
				  'dt_envio'                         => '',
				  'dt_retorno'                       => '',
				  'ds_retorno'                       => ''
                );
            }
            else
            {
                $this->reclamacao_analise_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/reclamacao_analise/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar_reclamacao()
	{
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_reclamacao_analise'] = $this->input->post("cd_reclamacao_analise", TRUE);   
			
			$this->reclamacao_analise_model->reclamacao($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('ecrm/reclamacao_analise/cadastro_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar()
	{
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_reclamacao_analise']            = $this->input->post("cd_reclamacao_analise", TRUE);   
			$args['cd_reclamacao_analise_classifica'] = $this->input->post("cd_reclamacao_analise_classifica", TRUE);   
			$args['cd_usuario_responsavel']           = $this->input->post("cd_usuario_responsavel", TRUE);   
			$args['cd_usuario_substituto']            = $this->input->post("cd_usuario_substituto", TRUE);   
			$args['dt_limite']                        = $this->input->post("dt_limite", TRUE);   
			$args['dt_prorrogacao']                   = $this->input->post("dt_prorrogacao", TRUE);   
			$args['observacao']                       = $this->input->post("observacao", TRUE);   
			$args['cd_usuario']                       = $this->session->userdata('codigo');
			
			$cd_reclamacao_analise = $this->reclamacao_analise_model->salvar($result, $args);
			
			redirect("ecrm/reclamacao_analise/cadastro/".$cd_reclamacao_analise, "refresh");
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar_reclamacao()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise']      = $this->input->post("cd_reclamacao_analise", TRUE);   
		$args['cd_reclamacao_analise_item'] = $this->input->post("cd_reclamacao_analise_item", TRUE);   
		$args['ano']                        = $this->input->post("ano", TRUE);   
		$args['numero']                     = $this->input->post("numero", TRUE);   
		$args['tipo']                       = $this->input->post("tipo", TRUE);   
		$args['fl_marcado']                 = $this->input->post("fl_marcado", TRUE);   
		$args['cd_usuario']                 = $this->session->userdata('codigo');
		
		$this->reclamacao_analise_model->salvar_reclamacao($result, $args);
		
		#### VERIFICA SE TEM ITENS ADICIONADOS ####
		$args = Array();
		$data = Array();
		$result = null;		
		$args['cd_reclamacao_analise'] = $this->input->post("cd_reclamacao_analise", TRUE);   
		$this->reclamacao_analise_model->reclamacao($result, $args);
		$ar_reg = $result->result_array();		
		
		$fl_reclamacao_analise_item = FALSE;
		foreach($ar_reg as $item)
		{
			if(intval($item['cd_reclamacao_analise_item']) > 0)
			{
				$fl_reclamacao_analise_item = TRUE;
			}
		}	
		
		echo json_encode(array("fl_enviar" => ($fl_reclamacao_analise_item == TRUE ? "S" : "N")));
	}
	
	function enviar($cd_reclamacao_analise)
	{
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_reclamacao_analise'] = $cd_reclamacao_analise; 
			$args['cd_usuario']            = $this->session->userdata('codigo');
			
			$this->reclamacao_analise_model->enviar($result, $args);
			
			redirect("ecrm/reclamacao_analise/cadastro/".$args['cd_reclamacao_analise'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
}
?>