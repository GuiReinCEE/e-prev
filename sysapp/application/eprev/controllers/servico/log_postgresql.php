<?php
class log_postgresql extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
        
        $this->load->model('projetos/log_postgresql_model');
    }
    
    function index()
    {
        if (gerencia_in(array('GI')))
        {
            $data = array();
            
            $this->load->view('servico/log_postgresql/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        if (gerencia_in(array('GI')))
        {
            $args = array();
            $data = array();
            $result = null;
            
            $this->log_postgresql_model->listar($result, $args);
            $data['ar_reg'] = $result->result_array();
            
            $this->load->view('servico/log_postgresql/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }    
    }
	
    function detalhe($arquivo = "")
    {
        if (gerencia_in(array('GI')))
        {
            if(trim($arquivo) == "")
			{
				exibir_mensagem("INFORME O ARQUIVO");
			}
			else
			{
				$args = array();
				$data = array();
				$result = null;
				
				$args['arquivo'] = trim($arquivo);
				
				$this->log_postgresql_model->listarLog($result, $args);
				$data['ar_reg'] = $result->result_array();
				
				$this->load->view('servico/log_postgresql/detalhe', $data);
			}
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }    
    }	
}
?>