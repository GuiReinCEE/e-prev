<?php
class Calendario_folha_pagamento extends Controller
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
            $this->load->view('ecrm/calendario_folha_pagamento/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    { 
        $this->load->model('autoatendimento/calendario_folha_pagamento_model');        

        $nr_ano = $this->input->post('nr_ano', TRUE);
   
        manter_filtros(array('nr_ano' => $nr_ano));

        $data['collection'] = $this->calendario_folha_pagamento_model->listar($nr_ano);

        $this->load->view('ecrm/calendario_folha_pagamento/index_result', $data);
        
    }

    public function cadastro($cd_calendario_folha_pagamento = 0)
    {
        if(gerencia_in(array('GP')))
        {
            if(intval($cd_calendario_folha_pagamento) == 0)
            {
                $data['row'] = array(
                    'cd_calendario_folha_pagamento' => intval($cd_calendario_folha_pagamento),
                    'dt_calendario_folha_pagamento' => '',
                    'ds_calendario_folha_pagamento' => ''
                );
            }
            else
            {
                $this->load->model('autoatendimento/calendario_folha_pagamento_model');

                $data['row'] = $this->calendario_folha_pagamento_model->carrega($cd_calendario_folha_pagamento);
            }

            $this->load->view('ecrm/calendario_folha_pagamento/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if (gerencia_in(array('GP')))
        {
            $this->load->model('autoatendimento/calendario_folha_pagamento_model');

            $cd_calendario_folha_pagamento = $this->input->post('cd_calendario_folha_pagamento', TRUE);

            $args = array(
                'cd_calendario_folha_pagamento' => intval($cd_calendario_folha_pagamento),
                'dt_calendario_folha_pagamento' => $this->input->post('dt_calendario_folha_pagamento', TRUE),
                'ds_calendario_folha_pagamento' => $this->input->post('ds_calendario_folha_pagamento', TRUE),
                'cd_usuario'                    => $this->session->userdata('codigo')
            );

            if(intval($cd_calendario_folha_pagamento) == 0)
            {
                $cd_calendario_folha_pagamento = $this->calendario_folha_pagamento_model->salvar($args);
            }
            else
            {
                $this->calendario_folha_pagamento_model->atualizar(intval($cd_calendario_folha_pagamento),$args);
            }

            redirect('ecrm/calendario_folha_pagamento/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>
