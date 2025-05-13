<?php
class Solicitacao_digitalizacao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GFC')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->view('ecrm/solicitacao_digitalizacao/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_usuarios()
    {       
        $this->load->model('projetos/solicitacao_digitalizacao_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

		foreach($this->solicitacao_digitalizacao_model->get_usuarios($cd_gerencia) as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
        echo json_encode($data);
    }

    public function listar()
    {
        $this->load->model('projetos/solicitacao_digitalizacao_model');

        $args = array(
            'cd_gerencia_responsavel'          => $this->input->post('cd_gerencia_responsavel', TRUE),
            'cd_usuario_responsavel'           => $this->input->post('cd_usuario_responsavel', TRUE),
            'dt_solicitacao_digitalizacao_ini' => $this->input->post('dt_solicitacao_digitalizacao_ini', TRUE),
            'dt_solicitacao_digitalizacao_fim' => $this->input->post('dt_solicitacao_digitalizacao_fim', TRUE)
        );
        
        manter_filtros($args);
      
        $data['collection'] = $this->solicitacao_digitalizacao_model->listar($args);

        $this->load->view('ecrm/solicitacao_digitalizacao/index_result', $data);   
    }

    public function cadastro($cd_solicitacao_digitalizacao = 0)
    {
        if($this->get_permissao())
        {
            if(intval($cd_solicitacao_digitalizacao) == 0)
            {
                $data['row'] = array(
                    'cd_solicitacao_digitalizacao' => intval($cd_solicitacao_digitalizacao),
                    'cd_gerencia_responsavel'      => '',
                    'cd_usuario_responsavel'       => '',
                    'ds_solicitacao_digitalizacao' => '',
                    'nr_solicitacao_digitalizacao' => '',
                    'dt_solicitacao_digitalizacao' => date('d/m/Y')
                );

                $data['responsavel'] = array();
            }
            else
            {
                $this->load->model('projetos/solicitacao_digitalizacao_model');

                $data['row'] = $this->solicitacao_digitalizacao_model->carrega($cd_solicitacao_digitalizacao);

                $data['responsavel'] = $this->solicitacao_digitalizacao_model->get_usuarios($data['row']['cd_gerencia_responsavel']);
            }

            $this->load->view('ecrm/solicitacao_digitalizacao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solicitacao_digitalizacao_model');

            $cd_solicitacao_digitalizacao = $this->input->post('cd_solicitacao_digitalizacao', TRUE);

            $args = array(
                'cd_solicitacao_digitalizacao' => intval($cd_solicitacao_digitalizacao),
                'cd_gerencia_responsavel'      => $this->input->post('cd_gerencia_responsavel', TRUE),
                'cd_usuario_responsavel'       => $this->input->post('cd_usuario_responsavel', TRUE),
                'ds_solicitacao_digitalizacao' => $this->input->post('ds_solicitacao_digitalizacao', TRUE),
                'nr_solicitacao_digitalizacao' => $this->input->post('nr_solicitacao_digitalizacao', TRUE),
                'dt_solicitacao_digitalizacao' => $this->input->post('dt_solicitacao_digitalizacao', TRUE),
                'cd_usuario'                   => $this->session->userdata('codigo')
            );

            if(intval($cd_solicitacao_digitalizacao) == 0)
            {
                $cd_solicitacao_digitalizacao = $this->solicitacao_digitalizacao_model->salvar($args);
            }
            else
            {
                $this->solicitacao_digitalizacao_model->atualizar(intval($cd_solicitacao_digitalizacao),$args);
            }

            redirect('ecrm/solicitacao_digitalizacao/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function relatorio()
    {
        if($this->get_permissao())
        {
            $this->load->view('ecrm/solicitacao_digitalizacao/relatorio');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function relatorio_listar()
    { 
        $this->load->model('projetos/solicitacao_digitalizacao_model');        

        $nr_ano = $this->input->post('nr_ano', TRUE);
           
        $data = array(
            'collection_mes'      => $this->solicitacao_digitalizacao_model->relatorio_mes($nr_ano),
            'collection_gerencia' => $this->solicitacao_digitalizacao_model->relatorio_gerencia($nr_ano),
            'collection_usuario'  => $this->solicitacao_digitalizacao_model->relatorio_usuario($nr_ano)
        );

        $this->load->view('ecrm/solicitacao_digitalizacao/relatorio_result', $data);
    }
}
?>