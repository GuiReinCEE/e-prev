<?php
class info_cronograma extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('informatica/info_cronograma_model');
    }
    
    function index()
    {        
        if(gerencia_in(array('GI')))
        {   
            $data   = Array();
            $args   = Array();
            $result = null;
            
            $this->info_cronograma_model->get_analista_cronograma($result, $args);
            $data['analista'] = $result->result_array();
            
            $this->load->view('atividade/info_cronograma/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if(gerencia_in(array('GI')))
        {   
            $data   = Array();
            $args   = Array();
            $result = null;
            
            $args["nr_mes"]      = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]      = $this->input->post("nr_ano", TRUE);
            $args["cd_analista"] = $this->input->post("cd_analista", TRUE);
            
            manter_filtros($args);
            
            $this->info_cronograma_model->cronograma($result, $args);
            $data['collection'] = $result->result_array();
                        
            for ($i = 0; count($data['collection']) > $i; $i++)
            {
                $args['cd_cronograma'] = $data['collection'][$i]['cd_cronograma'];
                
                $this->info_cronograma_model->cronograma_item($result, $args);

                $data['collection'][$i]['item'] = $result->result_array();
            }
            
            $this->load->view('atividade/info_cronograma/index_result.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($cd_cronograma = 0)
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;            
			
			$args['cd_cronograma'] = intval($cd_cronograma);
            
            $this->info_cronograma_model->get_analistas($result, $args);
            $data['analistas'] = $result->result_array();
            
            if (intval($cd_cronograma) == 0)
            {
                $data['row'] = Array(
                  'cd_cronograma' => 0,
                  'cd_analista'   => '',
                  'mes_ano'       => date('d/m/Y')
                );
            }
            else
            {
                $this->info_cronograma_model->carrega($result, $args);
                $data['row'] = $result->row_array();
                
                $this->info_cronograma_model->cronograma_item($result, $args);

                $data['collection'] = $result->result_array();
            }
            
            $this->load->view('atividade/info_cronograma/cadastro.php', $data);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;            
			
			$args["nr_mes"]              = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]              = $this->input->post("nr_ano", TRUE);
            $args["cd_analista"]         = $this->input->post("cd_analista", TRUE);
            $args["cd_cronograma"]       = $this->input->post("cd_cronograma", TRUE);
            $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');
            
            $cd_cronograma = $this->info_cronograma_model->salvar($result, $args);
            redirect("atividade/info_cronograma/cadastro/" . $cd_cronograma, "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir_item($cd_cronograma, $cd_cronograma_item)
    {
        if(gerencia_in(array('GI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_cronograma"]       = $cd_cronograma;
            $args["cd_cronograma_item"]  = $cd_cronograma_item;
            $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

            $this->info_cronograma_model->excluir_item($result, $args);

            redirect("atividade/info_cronograma/cadastro/" . $cd_cronograma, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir($cd_cronograma)
    {
        if(gerencia_in(array('GI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_cronograma"]       = $cd_cronograma;
            $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');

            $this->info_cronograma_model->excluir($result, $args);

            redirect("atividade/info_cronograma/index/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function item($cd_cronograma, $cd_cronograma_item = 0)
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_cronograma'] = intval($cd_cronograma);
            $data['cd_cronograma'] = intval($cd_cronograma);
            
            $args['cd_cronograma_item'] = intval($cd_cronograma_item);
                        
            if (intval($cd_cronograma_item) == 0)
            {
                $data['row'] = Array(
                  'cd_cronograma_item' => 0,
                  'nr_prioridade'      => '',
                  'descricao'          => '',
                  'fl_concluido'       => 'N',
                  'observacao'         => ''
                );
            }
            else
            {
				$this->info_cronograma_model->carrega($result, $args);
                $data['analista'] = $result->row_array();
			
                $this->info_cronograma_model->carrega_cronograma_item($result, $args);
                $data['row'] = $result->row_array();
            }
            
            $this->load->view('atividade/info_cronograma/item.php', $data);
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_item()
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
			
			$args["cd_cronograma"]       = $this->input->post("cd_cronograma", TRUE);
            $args["cd_cronograma_item"]  = $this->input->post("cd_cronograma_item", TRUE);
            $args["nr_prioridade"]       = $this->input->post("nr_prioridade", TRUE);
            $args["descricao"]           = $this->input->post("descricao", TRUE);
            $args["fl_concluido"]        = $this->input->post("fl_concluido", TRUE);
            $args["observacao"]          = $this->input->post("observacao", TRUE);
            $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');
            
            $this->info_cronograma_model->salvar_item($result, $args);
            redirect("atividade/info_cronograma/item/" . $args["cd_cronograma"]."/".$args["cd_cronograma_item"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    } 
	
    function setPrioridade()
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
            
			$args["cd_cronograma_item"] = $this->input->post("cd_cronograma_item", TRUE);
            $args["nr_prioridade"]      = $this->input->post("nr_prioridade", TRUE);

			$this->info_cronograma_model->setPrioridade($result, $args);

			echo "OK";
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
    function setConcluido()
    {
        if(gerencia_in(array('GI')))
        { 
            $args = Array();
            $data = Array();
            $result = null;
            
			$args["cd_cronograma_item"] = $this->input->post("cd_cronograma_item", TRUE);
            $args["fl_concluido"]      = $this->input->post("fl_concluido", TRUE);

			$this->info_cronograma_model->setConcluido($result, $args);

			echo "OK";
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}

?>