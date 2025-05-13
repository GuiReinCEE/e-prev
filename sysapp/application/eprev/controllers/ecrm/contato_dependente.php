<?php

class contato_dependente extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/contato_dependente_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GAP')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$this->load->view('ecrm/contato_dependente/index', $data);
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
            
            $args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);
            $args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
            $args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);
            $args['nome']                  = $this->input->post("nome", TRUE);
            $args['dt_ini']                = $this->input->post("dt_ini", TRUE);
            $args['dt_fim']                = $this->input->post("dt_fim", TRUE);
							
			manter_filtros($args);
            
            $data['collection'] = array();
            
			$this->contato_dependente_model->listar($result, $args);
			$collection = $result->result_array();
            
            $i = 0;
            
            foreach($collection as $item)
            {
                $args['cd_contato_dependente'] = $item['cd_contato_dependente'];
                
                $data['collection'][$i] = $item;
                
                $this->contato_dependente_model->listar_acompanhamento_dependente($result, $args);
                $data['collection'][$i]['acompanhamento'] = $result->result_array();
                
                $i++;
            }
            
			$this->load->view('ecrm/contato_dependente/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
	
	public function cadastro($cd_contato_dependente = 0, $cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
	{
        if (gerencia_in(array('GAP')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_contato_dependente'] = $cd_contato_dependente;
			$args['cd_empresa']            = $cd_empresa;
			$args['cd_registro_empregado'] = $cd_registro_empregado;
			$args['seq_dependencia']       = $seq_dependencia;			

            if((intval($args['cd_contato_dependente']) == 0) )
            {	
                $data['row'] = array(
                    'cd_contato_dependente' => intval($args['cd_contato_dependente']),
                    'cd_empresa'            => $cd_empresa,
                    'cd_registro_empregado' => $cd_registro_empregado,
                    'seq_dependencia'       => $seq_dependencia,
                    're'                    => '',
                    'nome'                  => '',
                    'telefone'              => '',
                    'celular'               => '',
                    'email'                 => '',
                    'email_profissional'    => '',
                    'endereco'              => '',
                    'bairro'                => '',
                    'cep'                   => '',
                    'cidade'                => '',
					'dt_obito'              => ''
                );
            }
            else
            {
                $this->contato_dependente_model->carrega($result, $args);
                $data['row'] = $result->row_array();
                

                
                
            }
		 
			#### LISTA DEPENDENTES ####
			$data['collection'] = array();
			
			$this->contato_dependente_model->listar_dependente($result, $args);
			$collection = $result->result_array();
			
			$i = 0;
			
			foreach($collection as $item)
			{
				$args['cd_empresa']            = $item['cd_empresa'];
				$args['cd_registro_empregado'] = $item['cd_registro_empregado'];
				$args['seq_dependencia']       = $item['seq_dependencia'];
				
				$data['collection'][$i] = $item;
				
				$this->contato_dependente_model->listar_acompanhamento($result, $args);
				$data['collection'][$i]['acompanhamento'] = $result->result_array();
				
				$i++;
			}		 
		 
            $this->load->view('ecrm/contato_dependente/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar()
	{
		if (gerencia_in(array('GAP')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_contato_dependente'] = $this->input->post("cd_contato_dependente", TRUE);
            $args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);
            $args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
            $args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);
			$args['cd_usuario']            = $this->session->userdata("codigo");
            
            $this->contato_dependente_model->verifica_cadastro($result, $args);
            $row = $result->row_array();
        
            if((!isset($row['cd_contato_dependente'])) OR (intval($row['cd_contato_dependente']) == 0))
            {
                $cd_contato_dependente = $this->contato_dependente_model->salvar($result, $args);
            }
            else
            {
                $cd_contato_dependente = intval($row['cd_contato_dependente']);
            }
                
			redirect("ecrm/contato_dependente/cadastro/".intval($cd_contato_dependente), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

    function acompanhamento($cd_contato_dependente, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        if (gerencia_in(array('GAP')))
        {
            $result   = null;
			$data     = array();
			$args     = array();
            
            $args['cd_contato_dependente'] = $cd_contato_dependente;
            $args['cd_empresa']            = $cd_empresa;
            $args['cd_registro_empregado'] = $cd_registro_empregado;
            $args['seq_dependencia']       = $seq_dependencia;
            
            $this->contato_dependente_model->carrega($result, $args);
            $data['row'] = $result->row_array();
            
            $this->contato_dependente_model->carrega_dependente($result, $args);
            $data['row_dependente'] = $result->row_array();
            
            $this->contato_dependente_model->listar_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();
            
            $this->load->view('ecrm/contato_dependente/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_acompanhamento()
    {
        if (gerencia_in(array('GAP')))
        {
            $result   = null;
			$data     = array();
			$args     = array();
            
            $args['cd_contato_dependente']                = $this->input->post("cd_contato_dependente", TRUE);
            $args['ds_contato_dependente_acompanhamento'] = $this->input->post("ds_contato_dependente_acompanhamento", TRUE);
            $args['cd_contato_dependente_retorno']        = $this->input->post("cd_contato_dependente_retorno", TRUE);
            $args['cd_empresa']                           = $this->input->post("cd_empresa", TRUE);
            $args['cd_registro_empregado']                = $this->input->post("cd_registro_empregado", TRUE);
            $args['seq_dependencia']                      = $this->input->post("seq_dependencia", TRUE);
			$args['cd_usuario']                           = $this->session->userdata("codigo");
            
            $this->contato_dependente_model->salvar_acompanhamento($result, $args);
            
            redirect("ecrm/contato_dependente/cadastro/".intval($args['cd_contato_dependente']), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	
    function checkContatoDependente()
    {
		$result   = null;
		$data     = array();
		$args     = array();
		
		$args['cd_empresa']                           = $this->input->post("cd_empresa", TRUE);
		$args['cd_registro_empregado']                = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']                      = $this->input->post("seq_dependencia", TRUE);

        $this->contato_dependente_model->verifica_cadastro($result, $args);
        $row = $result->row_array();		
		
		if(count($row) == 0)
		{
			$row["cd_contato_dependente"] = 0;
		}
		
		echo json_encode($row);
    }	
    
}
?>