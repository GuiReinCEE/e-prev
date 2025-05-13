<?php

class tabelas_atualizar extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
        
        $this->load->model('projetos/tabelas_atualizar_model');
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function index()
    {
        if($this->get_permissao())
        {
            $data = array();
            
            $this->load->view('servico/tabelas_atualizar/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar()
    {
        $args = array();
        $data = array();
        $result = null;
        
        $args['periodicidade'] = $this->input->post("periodicidade", TRUE);
        $args['tipo_bd'] = $this->input->post("tipo_bd", TRUE);
        $args['dt_inicio_ini'] = $this->input->post("dt_inicio_ini", TRUE);
        $args['dt_inicio_fim'] = $this->input->post("dt_inicio_fim", TRUE);
        
        manter_filtros($args);
        
        $this->tabelas_atualizar_model->listar($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('servico/tabelas_atualizar/partial_result', $data);
              
    }
    
    function cadastro($tabela = '')
    {
        if($this->get_permissao())
        {
            $args = array();
            $data = array();
            $result = null;
            
            $data['tabela'] = trim($tabela);
            $args['tabela'] = trim($tabela);
            
            if (trim($data['tabela']) == '')
            {
                $data['row'] = Array(
                  'tabela' => '',
                  'comando' => '',
                  'condicao' => '',
                  'contagem' => '',
                  'comando_inicial' => '',
                  'comando_final' => '',
                  'periodicidade' => '',
                  'dt_inicio' => '',
                  'dt_final' => '',
                  'hr_tempo' => '',
                  'postgres' => '',
                  'oracle' => '',
                  'access_callcenter' => '',
                  'truncar' => '',
                  'incrementar' => '',
                  'campo_controle_incremental' => ''

                );
            }
            else
            {
                $this->tabelas_atualizar_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }
            
            $this->load->view('servico/tabelas_atualizar/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        } 
    }
    
    function salvar()
    {
        if($this->get_permissao())
        {
            $args = array();
            $data = array();
            $result = null;
            
            $args['codigo'] = $this->input->post("codigo", TRUE);
            $args['tabela'] = $this->input->post("tabela", TRUE);
            $args['comando'] = $this->input->post("comando", TRUE);
            $args['condicao'] = $this->input->post("condicao", TRUE);
            $args['contagem'] = $this->input->post("contagem", TRUE);
            $args['comando_inicial'] = $this->input->post("comando_inicial", TRUE);
            $args['comando_final'] = $this->input->post("comando_final", TRUE);
            $args['periodicidade'] = $this->input->post("periodicidade", TRUE);
            $args['postgres'] = $this->input->post("postgres", TRUE);
            $args['oracle'] = $this->input->post("oracle", TRUE);
            $args['access_callcenter'] = $this->input->post("access_callcenter", TRUE);
            $args['truncar'] = $this->input->post("truncar", TRUE);
            $args['incrementar'] = $this->input->post("incrementar", TRUE);
            $args['campo_controle_incremental'] = $this->input->post("campo_controle_incremental", TRUE);
            
            $this->tabelas_atualizar_model->salvar($result, $args);
            
            redirect("servico/tabelas_atualizar/" , "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }    
    }
    
}
?>