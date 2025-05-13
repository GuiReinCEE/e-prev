<?php
class Novo_instituidor extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
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
            $this->load->view('gestao/novo_instituidor/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }	
    }

    public function get_usuarios()
    {       
        $this->load->model('gestao/novo_instituidor_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        echo json_encode($this->novo_instituidor_model->get_usuarios($cd_gerencia));
    }

    public function set_ordem($cd_novo_instituidor_estrutura)
    {
        $this->load->model('gestao/novo_instituidor_model');

        $args = array(
            'nr_novo_instituidor_estrutura' => $this->input->post('nr_novo_instituidor_estrutura', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );
        
        $this->novo_instituidor_model->set_ordem($cd_novo_instituidor_estrutura, $args);
    }

    public function listar()
    {		
        $this->load->model('gestao/novo_instituidor_model');

        $args = array(
        	'fl_desativado' => $this->input->post('fl_desativado', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->novo_instituidor_model->listar($args);

        foreach($data['collection'] as $key => $item)
        {               
            $data['collection'][$key]['ds_atividades_dependentes'] = array();

            $atividade = $this->novo_instituidor_model->get_atividade_checked($item['cd_novo_instituidor_estrutura']);

            foreach($atividade as $key1 => $item1) 
            {
                $data['collection'][$key]['ds_atividades_dependentes'][] = (COUNT($item1['ds_atividades_dependentes']) > 0 ? $item1['ds_atividades_dependentes'] : '');
            }
        }   

        $this->load->view('gestao/novo_instituidor/index_result', $data);		
    }

    public function cadastro($cd_novo_instituidor_estrutura = 0)
    {
    	if($this->get_permissao())
        {
        	$this->load->model('gestao/novo_instituidor_model');

            $data['atividade'] = $this->novo_instituidor_model->get_atividades($cd_novo_instituidor_estrutura); 
            $data['atividade_checked'] = array();

            if(intval($cd_novo_instituidor_estrutura) == 0)
            {
            	$row = $this->novo_instituidor_model->get_proximo_numero();

            	$data['row'] = array(
                    'cd_novo_instituidor_estrutura' => intval($cd_novo_instituidor_estrutura),
                    'nr_novo_instituidor_estrutura' => (count($row) > 0 ? $row['nr_novo_instituidor_estrutura'] : 1),
                    'ds_novo_instituidor_estrutura' => '',
                    'ds_atividade'                  => '',
                    'cd_gerencia'                   => '',
                    'cd_usuario_responsavel'        => '',
                    'cd_usuario_substituto'         => '',
                    'nr_prazo'                      => '',
                    'ds_observacao'                 => '',
                    'dt_desativado'                 => ''
                );

                $data['responsavel'] = array();
                $data['substituto']  = array();                
            }
            else
            {
            	$data['row'] = $this->novo_instituidor_model->carrega($cd_novo_instituidor_estrutura);                

            	$usuario = $this->novo_instituidor_model->get_usuarios($data['row']['cd_gerencia']);

            	$data['responsavel'] = $usuario;
                $data['substituto']  = $usuario;
            } 

            $atividade = $this->novo_instituidor_model->get_atividade_checked($cd_novo_instituidor_estrutura);
                
            foreach($atividade as $item)
            {               
                $data['atividade_checked'][] = $item['cd_novo_instituidor_estrutura_dep'];
            }                         
            
            $this->load->view('gestao/novo_instituidor/cadastro', $data);	
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
            $this->load->model('gestao/novo_instituidor_model');

            $cd_novo_instituidor_estrutura = $this->input->post('cd_novo_instituidor_estrutura', TRUE);

            $args = array(
                'nr_novo_instituidor_estrutura' => $this->input->post('nr_novo_instituidor_estrutura', TRUE),
                'ds_novo_instituidor_estrutura' => $this->input->post('ds_novo_instituidor_estrutura', TRUE),
                'ds_atividade'                  => $this->input->post('ds_atividade', TRUE),
                'cd_gerencia'                   => $this->input->post('cd_gerencia', TRUE),
                'cd_usuario_responsavel'        => $this->input->post('cd_usuario_responsavel', TRUE),
                'cd_usuario_substituto'         => $this->input->post('cd_usuario_substituto', TRUE),
                'nr_prazo'                      => $this->input->post('nr_prazo', TRUE),
                'ds_observacao'                 => $this->input->post('ds_observacao', TRUE),
                'cd_usuario'                    => $this->session->userdata('codigo')
            );

            $atividade_checked = $this->input->post('atividade_checked', TRUE);

            if(!is_array($atividade_checked))
            {
                $args['atividade_checked'] = array();
            }
            else
            {
                $args['atividade_checked'] = $atividade_checked;
            }
            
            if(intval($cd_novo_instituidor_estrutura) == 0)
            {
                $cd_novo_instituidor_estrutura = $this->novo_instituidor_model->salvar($args);
            }
            else
            {
                $this->novo_instituidor_model->atualizar($cd_novo_instituidor_estrutura, $args);
            }

            redirect('gestao/novo_instituidor/cadastro/'.$cd_novo_instituidor_estrutura, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function desativar($cd_novo_instituidor_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/novo_instituidor_model');

            $this->novo_instituidor_model->desativar($cd_novo_instituidor_estrutura, $this->session->userdata('codigo'));

            redirect('gestao/novo_instituidor/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function ativar($cd_novo_instituidor_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/novo_instituidor_model');

            $this->novo_instituidor_model->ativar($cd_novo_instituidor_estrutura, $this->session->userdata('codigo'));

            $atividade_checked = array();

            $row = $this->novo_instituidor_model->carrega($cd_novo_instituidor_estrutura);

            $atividade = $this->novo_instituidor_model->get_atividade_checked($cd_novo_instituidor_estrutura);
                
            $args = array(
                'nr_novo_instituidor_estrutura' => (count($row) > 0 ? $row['nr_novo_instituidor_estrutura'] : ''),
                'ds_novo_instituidor_estrutura' => (count($row) > 0 ? $row['ds_novo_instituidor_estrutura'] : ''),
                'ds_atividade'                  => (count($row) > 0 ? $row['ds_atividade'] : ''),
                'cd_gerencia'                   => (count($row) > 0 ? $row['cd_gerencia'] : ''),
                'cd_usuario_responsavel'        => (count($row) > 0 ? $row['cd_usuario_responsavel'] : ''),
                'cd_usuario_substituto'         => (count($row) > 0 ? $row['cd_usuario_substituto'] : ''),
                'nr_prazo'                      => (count($row) > 0 ? $row['nr_prazo'] : ''),
                'ds_observacao'                 => (count($row) > 0 ? $row['ds_observacao'] : ''),
                'cd_usuario'                    => $this->session->userdata('codigo')
            );            

            if(!is_array($atividade))
            {
                $args['atividade_checked'] = array(); 
            } 
            else
            {
                foreach ($atividade as $item) 
                {
                    $atividade_checked[] = $item['cd_novo_instituidor_estrutura_dep'];
                } 

                $args['atividade_checked'] = $atividade_checked;               
            }          
                        
            $cd_novo_instituidor_estrutura = $this->novo_instituidor_model->salvar($args);

            redirect('gestao/novo_instituidor/cadastro/'.$cd_novo_instituidor_estrutura, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}