<?php
class recadastro_devolucao extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/atendimento_recadastro_devolucao_model');
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
        #Gabriel Eliseu Lima da Luz
        elseif($this->session->userdata('codigo') == 312)
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
        elseif($this->session->userdata('codigo') == 375)
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

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
		if($this->get_permissao())
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;	
			
			$this->atendimento_recadastro_devolucao_model->devolucao_motivo( $result, $args );
			$data['ar_devolucao_motivo'] = $result->result_array();	
			
			$this->load->view('ecrm/recadastro_devolucao/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
    }	
	
    function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]                                 = $this->input->post('cd_empresa', TRUE);
		$args["cd_registro_empregado"]                      = $this->input->post('cd_registro_empregado', TRUE);
		$args["seq_dependencia"]                            = $this->input->post('seq_dependencia', TRUE);
		$args["nome"]                                       = $this->input->post('nome', TRUE);
		$args["dt_devolucao_ini"]                           = $this->input->post('dt_devolucao_ini', TRUE);
		$args["dt_devolucao_fim"]                           = $this->input->post('dt_devolucao_fim', TRUE);
		$args["cd_atendimento_recadastro_devolucao_motivo"] = $this->input->post('cd_atendimento_recadastro_devolucao_motivo', TRUE);

		manter_filtros($args);
		
		$this->atendimento_recadastro_devolucao_model->listar( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/recadastro_devolucao/index_result', $data);		
    }
	
    function cadastro($cd_atendimento_recadastro_devolucao = 0)
    {
		if($this->get_permissao())
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->atendimento_recadastro_devolucao_model->devolucao_motivo( $result, $args );
			$data['ar_devolucao_motivo'] = $result->result_array();	
			
			$args['cd_atendimento_recadastro_devolucao'] = intval($cd_atendimento_recadastro_devolucao);
			
			if(intval($cd_atendimento_recadastro_devolucao) == 0)
			{
				$data['row'] = Array(
					'cd_atendimento_recadastro_devolucao'        => intval($cd_atendimento_recadastro_devolucao),  
					'cd_atendimento_recadastro_devolucao_motivo' => '',  
					'cd_empresa'                                 => '',  
					'cd_registro_empregado'                      => '',  
					'seq_dependencia'                            => '',  
					'nome'                                       => '',
					'dt_devolucao'                               => '',
					'descricao'                                  => '',
					'observacao'                                 => '',
					'dt_inclusao'                                => '',
					'cd_usuario_inclusao'                        => '',
					'dt_exclusao'                                => '',
					'cd_usuario_exclusao'                        => '',
					'nome_usuario'                               => '',
					'nome_usuario_exclusao'                      => '',
					'dt_alteracao'                               => '',
					'nome_usuario_alteracao'                     => ''
				);
			}
			else
			{
				$this->atendimento_recadastro_devolucao_model->carrega($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/recadastro_devolucao/cadastro',$data);
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
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_atendimento_recadastro_devolucao"]        = $this->input->post("cd_atendimento_recadastro_devolucao", TRUE);
			$args["cd_atendimento_recadastro_devolucao_motivo"] = $this->input->post("cd_atendimento_recadastro_devolucao_motivo", TRUE);
			$args["cd_empresa"]                                 = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]                      = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]                            = $this->input->post("seq_dependencia", TRUE);
			$args["nome"]                                       = $this->input->post("nome", TRUE);
			$args["dt_devolucao"]                               = $this->input->post("dt_devolucao", TRUE);
			$args["descricao"]                                  = $this->input->post("descricao", TRUE);
			$args["observacao"]                                 = $this->input->post("observacao", TRUE);
			$args["cd_usuario"]                                 = $this->session->userdata('codigo');
			
			$retorno = $this->atendimento_recadastro_devolucao_model->salvar( $result, $args );
			
			redirect("ecrm/recadastro_devolucao/cadastro/".$retorno, "refresh");
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
    }
	
	function excluir($cd_atendimento_recadastro_devolucao = "")
    {
		if($this->get_permissao())
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_atendimento_recadastro_devolucao"] = $cd_atendimento_recadastro_devolucao;
			$args["cd_usuario"]                          = $this->session->userdata('codigo');
			
			$this->atendimento_recadastro_devolucao_model->excluir( $result, $args );
			
			redirect("ecrm/recadastro_devolucao/", "refresh");
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
    }	
}
?>