<?php
class Atendimento_recadastro extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/atendimento_recadastro_model');
    }

    private function get_permissao()
    {
    	#Eloisa Helena R. de Rodrigues
    	if($this->session->userdata('codigo') == 40)
        {
            return TRUE;
        }
        #Eliane Cristiane Pacheco Alcino
    	if($this->session->userdata('codigo') == 39)
        {
            return TRUE;
        }
        #Ygor Roldao Bueno
    	if($this->session->userdata('codigo') == 249)
        {
            return TRUE;
        }
        #Cristina Hochmuller da Silva
    	if($this->session->userdata('codigo') == 287)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves
    	if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        #Silvia Elisandra Gomes Teixeira
    	if($this->session->userdata('codigo') == 354)
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Viviane Schneider de Lara
        else if($this->session->userdata('codigo') == 375)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
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
			$this->load->view('ecrm/atendimento_recadastro/index');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
	
	function listar()
    {		
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['dt_criacao_ini']        = $this->input->post("dt_criacao_ini", TRUE);
		$args['dt_criacao_fim']        = $this->input->post("dt_criacao_fim", TRUE);
		$args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);
		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);
		
		manter_filtros($args);
		
		$this->atendimento_recadastro_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/atendimento_recadastro/index_result', $data);	
    }
	
	function cadastro($cd_atendimento_recadastro = 0)
    {	
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_atendimento_recadastro'] = intval($cd_atendimento_recadastro);
			
			if(intval($args['cd_atendimento_recadastro']) == 0)
			{
				$data['row'] = array(
					'cd_atendimento_recadastro' => $args['cd_atendimento_recadastro'],
					'cd_empresa'                => '',
					'cd_registro_empregado'     => '',
					'nome'                      => '',
					'seq_dependencia'           => '',
					'observacao'                => '',
					'servico_social'            => '',
					'dt_cancelamento'           => '',
					'dt_periodo'                => '',
					'dt_criacao'                => '',
					'dt_atualizacao'            => '',
					'motivo_cancelamento'       => ''
				);
			}
			else
			{
				$this->atendimento_recadastro_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/atendimento_recadastro/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	function salvar()
	{
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_atendimento_recadastro'] = $this->input->post("cd_atendimento_recadastro", TRUE);
			$args['cd_empresa']                = $this->input->post("cd_empresa", TRUE);
			$args['cd_registro_empregado']     = $this->input->post("cd_registro_empregado", TRUE);
			$args['seq_dependencia']           = $this->input->post("seq_dependencia", TRUE);
			$args['nome']                      = $this->input->post("nome", TRUE);
			$args['observacao']                = $this->input->post("observacao", TRUE);
			$args['servico_social']            = $this->input->post("servico_social", TRUE);
			$args['cd_usuario']                = $this->session->userdata("codigo");
			
			$this->atendimento_recadastro_model->salvar($result, $args);
			
			redirect('ecrm/atendimento_recadastro/', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	function cancelar($cd_atendimento_recadastro)
	{
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_atendimento_recadastro'] = $cd_atendimento_recadastro;
			
			$this->atendimento_recadastro_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('ecrm/atendimento_recadastro/cancelar', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	function salvar_cancelamento()
	{
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_atendimento_recadastro'] = $this->input->post("cd_atendimento_recadastro", TRUE);
			$args['motivo_cancelamento']       = $this->input->post("motivo_cancelamento", TRUE);
			$args['cd_usuario']                = $this->session->userdata("codigo");
			
			$this->atendimento_recadastro_model->salvar_cancelamento($result, $args);
			
			redirect('ecrm/atendimento_recadastro/', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	function verifica_re_ano()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);
		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);
		$args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);
		$args['ano']                   = date('Y');
		
		$this->atendimento_recadastro_model->verifica_re_ano($result, $args);
		$row = $result->row_array();
		
		echo intval($row['total']);
	}
}
?>