<?php
class Protocolo_gc_investimentos extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC', 'GIN')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_usuarios()
    {       
        $this->load->model('projetos/protocolo_gc_investimentos_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        echo json_encode($this->protocolo_gc_investimentos_model->get_usuarios($cd_gerencia));
    }
	
    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->view('atividade/protocolo_gc_investimentos/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }	
    }
	
    public function listar()
    {		
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $args = array(
                'dt_envio_gc_ini'  => $this->input->post('dt_envio_gc_ini', TRUE),
                'dt_envio_gc_fim'  => $this->input->post('dt_envio_gc_fim', TRUE),
                'dt_recebido_ini'  => $this->input->post('dt_recebido_ini', TRUE),
                'dt_recebido_fim'  => $this->input->post('dt_recebido_fim', TRUE),
                'dt_envio_sg_ini'  => $this->input->post('dt_envio_sg_ini', TRUE),
                'dt_envio_sg_fim'  => $this->input->post('dt_envio_sg_fim', TRUE),
                'dt_expedicao_ini' => $this->input->post('dt_expedicao_ini', TRUE),
                'dt_expedicao_fim' => $this->input->post('dt_expedicao_fim', TRUE),
                'dt_encerrar_ini'  => $this->input->post('dt_encerrar_ini', TRUE),
                'dt_encerrar_fim'  => $this->input->post('dt_encerrar_fim', TRUE)
            );

            manter_filtros($args);

            $data['collection'] = $this->protocolo_gc_investimentos_model->listar($args);

            $this->load->view('atividade/protocolo_gc_investimentos/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
    }
	
    public function cadastro($cd_protocolo_gc_investimentos = 0)
    {
        if($this->get_permissao())
        {
            if(intval($cd_protocolo_gc_investimentos) == 0)
            {
                $data['row'] = array(
                    'cd_protocolo_gc_investimentos' => intval($cd_protocolo_gc_investimentos),
                    'documento'                     => '',
                    'observacao'                    => '',
                    'dt_envio_gc'                   => '',
                    'dt_recebido'                   => '',
                    'dt_envio_sg'                   => '',
                    'dt_expedicao'                  => '',
                    'dt_encerrar'                   => '',
                    'cd_gerencia_sg'                => '',
                    'cd_usuario_sg'                 => '',
					'dt_recusado'                   => '',
					'ds_justificativa'              => '',
                    'fl_retorno'                    => '',
                    'arquivo'                       => '',
                    'arquivo_nome'                  => '',
                    'ds_doc_pendente'               => ''
                );

                $data['usuario'] = array();
            }
            else
            {
                $this->load->model('projetos/protocolo_gc_investimentos_model');

                $data['row'] = $this->protocolo_gc_investimentos_model->carrega(intval($cd_protocolo_gc_investimentos));

                $data['usuario'] = $this->protocolo_gc_investimentos_model->get_usuarios($data['row']['cd_gerencia_sg']);
            }
			
            if(((trim($data['row']['dt_envio_gc']) != '') AND (gerencia_in(array('GC')))) 
                OR
                (trim($data['row']['dt_envio_gc']) == '')
            )
            {
                $this->load->view('atividade/protocolo_gc_investimentos/cadastro', $data);
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
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
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $cd_protocolo_gc_investimentos = $this->input->post('cd_protocolo_gc_investimentos', TRUE);

            $args = array(
                'documento'       => $this->input->post('documento', TRUE),
                'observacao'      => $this->input->post('observacao', TRUE),
                'dt_envio_sg'     => $this->input->post('dt_envio_sg', TRUE),
                'dt_expedicao'    => $this->input->post('dt_expedicao', TRUE),
                'cd_usuario_sg'   => $this->input->post('cd_usuario_sg', TRUE),
                'fl_retorno'      => $this->input->post('fl_retorno', TRUE),
                'arquivo'         => $this->input->post('arquivo', TRUE),
                'arquivo_nome'    => $this->input->post('arquivo_nome', TRUE),
                'ds_doc_pendente' => $this->input->post('ds_doc_pendente', TRUE),
                'cd_usuario'      => $this->session->userdata('codigo')
            );

            if(intval($cd_protocolo_gc_investimentos) == 0)
            {
                $cd_protocolo_gc_investimentos = $this->protocolo_gc_investimentos_model->salvar($args);
            }
            else
            {
                $this->protocolo_gc_investimentos_model->atualizar($cd_protocolo_gc_investimentos, $args);
            }

            redirect('atividade/protocolo_gc_investimentos/cadastro/'.$cd_protocolo_gc_investimentos, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
    public function enviar_gc($cd_protocolo_gc_investimentos)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $this->protocolo_gc_investimentos_model->enviar_gc($cd_protocolo_gc_investimentos, $this->session->userdata('codigo'));

            if(trim($this->session->userdata('divisao')) == 'GC')
            {
                redirect('atividade/protocolo_gc_investimentos/cadastro/'.$cd_protocolo_gc_investimentos, 'refresh');
            }
            else
            {
                redirect('atividade/protocolo_gc_investimentos', 'refresh');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function recusar($cd_protocolo_gc_investimentos)
    {
        if(gerencia_in(array('GC')))
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');
            
            $data['row'] = $this->protocolo_gc_investimentos_model->carrega($cd_protocolo_gc_investimentos);
            
            $this->load->view('atividade/protocolo_gc_investimentos/recusar', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function salvar_recusa()
    {
        if(gerencia_in(array('GC')))
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $cd_protocolo_gc_investimentos = $this->input->post('cd_protocolo_gc_investimentos', TRUE);

            $args = array(
                'ds_justificativa' => $this->input->post('ds_justificativa', TRUE),
                'cd_usuario'       => $this->session->userdata('codigo')
            );

            $this->protocolo_gc_investimentos_model->recusar($cd_protocolo_gc_investimentos, $args);

            redirect('atividade/protocolo_gc_investimentos/cadastro/'.$cd_protocolo_gc_investimentos, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
    public function receber($cd_protocolo_gc_investimentos)
    {
        if(gerencia_in(array('GC')))
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $this->protocolo_gc_investimentos_model->receber($cd_protocolo_gc_investimentos, $this->session->userdata('codigo'));

            redirect('atividade/protocolo_gc_investimentos/cadastro/'.$cd_protocolo_gc_investimentos, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
    public function encerrar($cd_protocolo_gc_investimentos)
    {
        if(gerencia_in(array('GC')))
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $this->protocolo_gc_investimentos_model->encerrar($cd_protocolo_gc_investimentos, $this->session->userdata('codigo'));

            redirect("atividade/protocolo_gc_investimentos/cadastro/".$cd_protocolo_gc_investimentos, "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function acompanhamento($cd_protocolo_gc_investimentos)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $data = array(
                'row'        => $this->protocolo_gc_investimentos_model->carrega($cd_protocolo_gc_investimentos),
                'collection' => $this->protocolo_gc_investimentos_model->listar_acompanhamento($cd_protocolo_gc_investimentos)
            );
            
            $this->load->view('atividade/protocolo_gc_investimentos/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function salvar_acompanhamento()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_gc_investimentos_model');

            $cd_protocolo_gc_investimentos = $this->input->post('cd_protocolo_gc_investimentos', TRUE);

            $args = array(
                'acompanhamento' => $this->input->post('acompanhamento', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')
            );

            $this->protocolo_gc_investimentos_model->salvar_acompanhamento($cd_protocolo_gc_investimentos, $args);

            redirect('atividade/protocolo_gc_investimentos/acompanhamento/'.$cd_protocolo_gc_investimentos, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>