<?php

class Atendimento_familiares_falecidos extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
        if(gerencia_in(array('GP')))
        {
    		$data = array();

    		$this->load->view('ecrm/atendimento_familiares_falecidos/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()    
    {
        $this->load->model('projetos/atendimento_familiares_falecidos_model');
        
        $args = array();
        $data = array();
        
        $args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);   
        $args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);   
        $args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);   
        $args['dt_ini']                = $this->input->post("dt_ini", TRUE);   
        $args['dt_fim']                = $this->input->post("dt_fim", TRUE);   
        $args['dt_encerramento_ini']   = $this->input->post("dt_encerramento_ini", TRUE);   
        $args['dt_encerramento_fim']   = $this->input->post("dt_encerramento_fim", TRUE);   
        $args['fl_encerrada']          = $this->input->post("fl_encerrada", TRUE);   

        manter_filtros($args);

        $data['collection'] = $this->atendimento_familiares_falecidos_model->listar($args);

        $this->load->view('ecrm/atendimento_familiares_falecidos/index_result', $data);
    }

    public function cadastro($cd_atendimento_familiares_falecidos = 0, $cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "", $cd_atendimento = 0)
    {
        if(gerencia_in(array('GP')))
        {
            $data = array();
            $args = array();

            if(intval($cd_atendimento_familiares_falecidos) == 0)
            {
                $data['row'] = array(
                    'cd_atendimento_familiares_falecidos' => $cd_atendimento_familiares_falecidos,
                    'cd_empresa'                          => $cd_empresa,
                    'cd_registro_empregado'               => $cd_registro_empregado,
                    'seq_dependencia'                     => $seq_dependencia,
                    'cd_atendimento'                      => $cd_atendimento,
                    'contato'                             => '',
                    'observacao'                          => '',
                    'dt_encerramento'                     => ''
                );

                $data['retorno'] = array();
            }
            else
            {
                $this->load->model(array(
                    'projetos/atendimento_familiares_falecidos_model',
                    'projetos/atendimento_familiares_falecidos_retorno_model'
                ));

                $data['row'] = $this->atendimento_familiares_falecidos_model->carrega(intval($cd_atendimento_familiares_falecidos));

                $data['retorno'] = $this->atendimento_familiares_falecidos_retorno_model->listar(intval($cd_atendimento_familiares_falecidos));
            }

            $this->load->view('ecrm/atendimento_familiares_falecidos/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if(gerencia_in(array('GP')))
        {
            $this->load->model('projetos/atendimento_familiares_falecidos_model');

            $args = array();

            $cd_atendimento_familiares_falecidos = intval($this->input->post('cd_atendimento_familiares_falecidos', TRUE)); 

            $args['cd_empresa']                          = $this->input->post('cd_empresa', TRUE); 
            $args['cd_registro_empregado']               = $this->input->post('cd_registro_empregado', TRUE); 
            $args['seq_dependencia']                     = $this->input->post('seq_dependencia', TRUE); 
            $args['cd_atendimento']                      = $this->input->post('cd_atendimento', TRUE); 
            $args['contato']                             = $this->input->post('contato', TRUE); 
            $args['observacao']                          = $this->input->post('observacao', TRUE); 
            $args['cd_usuario']                          = $this->session->userdata('codigo');

            if(intval($cd_atendimento_familiares_falecidos) == 0)
            {
                $this->atendimento_familiares_falecidos_model->salvar($args);
            }
            else
            {
                $this->atendimento_familiares_falecidos_model->atualizar($cd_atendimento_familiares_falecidos, $args);
            }

            redirect('ecrm/atendimento_familiares_falecidos');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_atendimento_familiares_falecidos)
    {
        if(gerencia_in(array('GP')))
        {
            $this->load->model('projetos/atendimento_familiares_falecidos_model');

            $args = array();

            $args['cd_usuario'] = $this->session->userdata('codigo');

            $this->atendimento_familiares_falecidos_model->excluir($cd_atendimento_familiares_falecidos, $args);

            redirect('ecrm/atendimento_familiares_falecidos');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function encerrar($cd_atendimento_familiares_falecidos)
    {
        if(gerencia_in(array('GP')))
        {
            $this->load->model('projetos/atendimento_familiares_falecidos_model');

            $args = array();

            $args['cd_usuario'] = $this->session->userdata('codigo');

            $this->atendimento_familiares_falecidos_model->encerrar($cd_atendimento_familiares_falecidos, $args);

            redirect('ecrm/atendimento_familiares_falecidos');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function retorno($cd_atendimento_familiares_falecidos, $cd_atendimento_familiares_falecidos_retorno = 0)
    {
        if(gerencia_in(array('GP')))
        {
            $data = array();
            $args = array();

            $this->load->model('projetos/atendimento_familiares_falecidos_model');

            $data['contato'] = $this->atendimento_familiares_falecidos_model->carrega(intval($cd_atendimento_familiares_falecidos));

            if(intval($cd_atendimento_familiares_falecidos_retorno) == 0)
            {
                $data['row'] = array(
                    'cd_atendimento_familiares_falecidos'         => $cd_atendimento_familiares_falecidos,
                    'cd_atendimento_familiares_falecidos_retorno' => $cd_atendimento_familiares_falecidos_retorno,
                    'ds_atendimento_familiares_falecidos_retorno' => ''
                );
            }
            else
            {
                $this->load->model('projetos/atendimento_familiares_falecidos_retorno_model');

                $data['row'] = $this->atendimento_familiares_falecidos_retorno_model->carrega(intval($cd_atendimento_familiares_falecidos_retorno));
            }

            $this->load->view('ecrm/atendimento_familiares_falecidos/retorno', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function retorno_salvar()
    {
        if(gerencia_in(array('GP')))
        {
            $this->load->model('projetos/atendimento_familiares_falecidos_retorno_model');

            $args = array();

            $cd_atendimento_familiares_falecidos_retorno = intval($this->input->post('cd_atendimento_familiares_falecidos_retorno', TRUE)); 

            $args['ds_atendimento_familiares_falecidos_retorno'] = $this->input->post('ds_atendimento_familiares_falecidos_retorno', TRUE); 
            $args['cd_atendimento_familiares_falecidos']         = $this->input->post('cd_atendimento_familiares_falecidos', TRUE); 
            $args['cd_usuario']                                  = $this->session->userdata('codigo');

            if(intval($cd_atendimento_familiares_falecidos_retorno) == 0)
            {
                $this->atendimento_familiares_falecidos_retorno_model->salvar($args);
            }
            else
            {
                $this->atendimento_familiares_falecidos_retorno_model->atualizar($cd_atendimento_familiares_falecidos_retorno, $args);
            }

            redirect('ecrm/atendimento_familiares_falecidos/cadastro/'.$args['cd_atendimento_familiares_falecidos']);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function acompanhamento($cd_atendimento_familiares_falecidos, $cd_atendimento_familiares_falecidos_acompanhamento = 0)
    {
        if(gerencia_in(array('GP')))
        {
            $data = array();
            $args = array();

            $this->load->model(array(
                'projetos/atendimento_familiares_falecidos_model',
                'projetos/atendimento_familiares_falecidos_acompanhamento_model'
            ));

            $data['contato'] = $this->atendimento_familiares_falecidos_model->carrega(intval($cd_atendimento_familiares_falecidos));

            $data['collection'] = $this->atendimento_familiares_falecidos_acompanhamento_model->listar(intval($cd_atendimento_familiares_falecidos));

            if(intval($cd_atendimento_familiares_falecidos_acompanhamento) == 0)
            {
                $data['row'] = array(
                    'cd_atendimento_familiares_falecidos'         => $cd_atendimento_familiares_falecidos,
                    'cd_atendimento_familiares_falecidos_acompanhamento' => $cd_atendimento_familiares_falecidos_acompanhamento,
                    'ds_atendimento_familiares_falecidos_acompanhamento' => ''
                );
            }
            else
            {
                $data['row'] = $this->atendimento_familiares_falecidos_acompanhamento_model->carrega(intval($cd_atendimento_familiares_falecidos_acompanhamento));
            }

            $this->load->view('ecrm/atendimento_familiares_falecidos/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function acompanhamento_salvar()
    {
        if(gerencia_in(array('GP')))
        {
            $this->load->model('projetos/atendimento_familiares_falecidos_acompanhamento_model');

            $args = array();

            $cd_atendimento_familiares_falecidos_acompanhamento = intval($this->input->post('cd_atendimento_familiares_falecidos_acompanhamento', TRUE)); 

            $args['ds_atendimento_familiares_falecidos_acompanhamento'] = $this->input->post('ds_atendimento_familiares_falecidos_acompanhamento', TRUE); 
            $args['cd_atendimento_familiares_falecidos']                = $this->input->post('cd_atendimento_familiares_falecidos', TRUE); 
            $args['cd_usuario']                                         = $this->session->userdata('codigo');

            if(intval($cd_atendimento_familiares_falecidos_acompanhamento) == 0)
            {
                $this->atendimento_familiares_falecidos_acompanhamento_model->salvar($args);
            }
            else
            {
                $this->atendimento_familiares_falecidos_acompanhamento_model->atualizar($cd_atendimento_familiares_falecidos_acompanhamento, $args);
            }

            redirect('ecrm/atendimento_familiares_falecidos/acompanhamento/'.$args['cd_atendimento_familiares_falecidos']);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}