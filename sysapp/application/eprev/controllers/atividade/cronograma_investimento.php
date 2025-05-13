<?php
class cronograma_investimento extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/cronograma_investimento_model');
    }
    
    function index()
    {        
        if(gerencia_in(array('GIN')))
        {   
            $data   = Array();
            $args   = Array();
            $result = null;
            
            $this->cronograma_investimento_model->get_analista_cronograma($result, $args);
            $data['analista'] = $result->result_array();
            
            $this->load->view('atividade/cronograma_investimento/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if(gerencia_in(array('GIN')))
        {   
            $data   = Array();
            $args   = Array();
            $result = null;
            
            $args["nr_mes"]       = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]       = $this->input->post("nr_ano", TRUE);
            $args["cd_analista"]  = $this->input->post("cd_analista", TRUE);
            $args["fl_concluido"] = $this->input->post("fl_concluido", TRUE);

            manter_filtros($args);
            
            $this->cronograma_investimento_model->cronograma($result, $args);
            $data['collection'] = $result->result_array();
                        
            for ($i = 0; count($data['collection']) > $i; $i++)
            {
                $args['cd_cronograma_investimento'] = $data['collection'][$i]['cd_cronograma_investimento'];
                
                $this->cronograma_investimento_model->cronograma_item($result, $args);

                $data['collection'][$i]['item'] = $result->result_array();
            }
            
            $this->load->view('atividade/cronograma_investimento/index_result.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($cd_cronograma_investimento = 0)
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;            
			
			$args['cd_cronograma_investimento'] = intval($cd_cronograma_investimento);
            $args['fl_concluido']               = '';
            
            $this->cronograma_investimento_model->get_analistas($result, $args);
            $data['analistas'] = $result->result_array();
            
            if (intval($cd_cronograma_investimento) == 0)
            {
                $data['row'] = Array(
                  'cd_cronograma_investimento' => 0,
                  'cd_analista'                => '',
                  'mes_ano'                    => date('d/m/Y')
                );
            }
            else
            {
                $this->cronograma_investimento_model->carrega($result, $args);
                $data['row'] = $result->row_array();
                
                $this->cronograma_investimento_model->cronograma_item($result, $args);
                $data['collection'] = $result->result_array();
            }
            
            $this->load->view('atividade/cronograma_investimento/cadastro.php', $data);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;            
			
			$args["nr_mes"]                       = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]                       = $this->input->post("nr_ano", TRUE);
            $args["cd_analista"]                  = $this->input->post("cd_analista", TRUE);
            $args["cd_cronograma_investimento"]   = $this->input->post("cd_cronograma_investimento", TRUE);
            $args["cd_usuario_inclusao"]          = $this->session->userdata('codigo');
            
            $cd_cronograma_investimento = $this->cronograma_investimento_model->salvar($result, $args);
            redirect("atividade/cronograma_investimento/cadastro/" . $cd_cronograma_investimento, "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function troca_analista()
	{
		if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;            
			
            $args["cd_analista"]                     = $this->input->post("cd_analista", TRUE);
            $args["cd_cronograma_investimento"]      = $this->input->post("cd_cronograma_investimento", TRUE);
			$args["cd_cronograma_investimento_item"] = $this->input->post("cd_cronograma_investimento_item", TRUE);
            $args["cd_usuario_inclusao"]             = $this->session->userdata('codigo');
            
            $this->cronograma_investimento_model->troca_analista($result, $args);
            redirect("atividade/cronograma_investimento/cadastro/".$args["cd_cronograma_investimento"], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function excluir_item($cd_cronograma_investimento, $cd_cronograma_investimento_item)
    {
        if(gerencia_in(array('GIN')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_cronograma_investimento"]       = $cd_cronograma_investimento;
            $args["cd_cronograma_investimento_item"]  = $cd_cronograma_investimento_item;
            $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

            $this->cronograma_investimento_model->excluir_item($result, $args);

            redirect("atividade/cronograma_investimento/cadastro/" . $cd_cronograma_investimento, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir($cd_cronograma_investimento)
    {
        if(gerencia_in(array('GIN')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_cronograma_investimento"]       = $cd_cronograma_investimento;
            $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

            $this->cronograma_investimento_model->excluir($result, $args);

            redirect("atividade/cronograma_investimento/index/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function item($cd_cronograma_investimento, $cd_cronograma_investimento_item = 0)
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_cronograma_investimento'] = intval($cd_cronograma_investimento);
            $data['cd_cronograma_investimento'] = intval($cd_cronograma_investimento);
            
            $args['cd_cronograma_investimento_item'] = intval($cd_cronograma_investimento_item);
			
			$this->cronograma_investimento_model->get_analistas($result, $args);
            $data['analistas'] = $result->result_array();
                        
            if (intval($cd_cronograma_investimento_item) == 0)
            {
                $data['row'] = Array(
                  'cd_cronograma_investimento_item' => 0,
                  'nr_prioridade'      => '',
                  'descricao'          => '',
                  'fl_concluido'       => 'N',
                  'observacao'         => '',
				  'dt_limite'          => ''
                );
            }
            else
            {
				$this->cronograma_investimento_model->carrega($result, $args);
                $data['analista'] = $result->row_array();
			
                $this->cronograma_investimento_model->carrega_cronograma_item($result, $args);
                $data['row'] = $result->row_array();
            }
            
            $this->load->view('atividade/cronograma_investimento/item.php', $data);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_item()
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["cd_cronograma_investimento"]       = $this->input->post("cd_cronograma_investimento", TRUE);
            $args["cd_cronograma_investimento_item"]  = $this->input->post("cd_cronograma_investimento_item", TRUE);
            $args["nr_prioridade"]       = $this->input->post("nr_prioridade", TRUE);
            $args["descricao"]           = $this->input->post("descricao", TRUE);
            $args["fl_concluido"]        = $this->input->post("fl_concluido", TRUE);
            $args["observacao"]          = $this->input->post("observacao", TRUE);
            $args["dt_limite"]           = $this->input->post("dt_limite", TRUE);
            $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');
            
            $this->cronograma_investimento_model->salvar_item($result, $args);
            redirect("atividade/cronograma_investimento/item/" . $args["cd_cronograma_investimento"]."/".$args["cd_cronograma_investimento_item"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    } 
	
    function setPrioridade()
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
            
			$args["cd_cronograma_investimento_item"] = $this->input->post("cd_cronograma_investimento_item", TRUE);
            $args["nr_prioridade"]      = $this->input->post("nr_prioridade", TRUE);

			$this->cronograma_investimento_model->setPrioridade($result, $args);

			echo "OK";
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
    function setConcluido()
    {
        if(gerencia_in(array('GIN')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
            
			$args["cd_cronograma_investimento_item"] = $this->input->post("cd_cronograma_investimento_item", TRUE);
            $args["fl_concluido"]      = $this->input->post("fl_concluido", TRUE);

			$this->cronograma_investimento_model->setConcluido($result, $args);

			echo "OK";
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}

?>