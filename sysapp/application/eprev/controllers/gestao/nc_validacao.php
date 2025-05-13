<?php
class Nc_validacao extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        //anunes
        if($this->session->userdata('codigo') == 26)
        {
            return TRUE;
        }
        //lucios
        else if($this->session->userdata('codigo') == 415)
        {
            return TRUE;
        }
		#mvoigt
		else if($this->session->userdata('codigo') == 319)
		{
			return true;
		}
        #jseidler
        else if($this->session->userdata('codigo') == 298)
        {
            return true;
        }
        //lrodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index($cd_nao_conformidade)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/nc_validacao_model');

            $data = array();

            $data['usuario'] = $this->nc_validacao_model->get_usuario();
            $data['row'] = $this->nc_validacao_model->carrega($cd_nao_conformidade);

            $this->load->view('gestao/nc_validacao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function cadastro($cd_nao_conformidade)
    {
        $this->index($cd_nao_conformidade);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/rig_model');

            $cd_rig = $this->input->post('cd_rig', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_rig) == 0)
            {
                $this->rig_model->salvar($args);
            }
            else
            {
                $this->rig_model->atualizar($cd_rig, $args);
            }

            redirect('gestao/rig', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_rig)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/rig_model'
            ));

            $cd_evento = 439;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/rig/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações RIG',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->rig_model->enviar($cd_rig, $cd_usuario);

            redirect('gestao/rig/cadastro/'.$cd_rig, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    