<?php
class divulgacao_grupo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/divulgacao_grupo_model');
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
	
	public function index()
    {
		if($this->get_permissao())
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('ecrm/divulgacao_grupo/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['ds_grupo']    = $this->input->post("ds_grupo", TRUE);   
		$args['fl_excluido'] = $this->input->post("fl_excluido", TRUE);   
		
		manter_filtros($args);

		$this->divulgacao_grupo_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('ecrm/divulgacao_grupo/index_result', $data);
    }
	
	function cadastro($cd_divulgacao_grupo = 0)
    {
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_divulgacao_grupo'] = intval($cd_divulgacao_grupo);
			
            if (intval($cd_divulgacao_grupo) == 0)
            {
                $data['row'] = Array(
                  'cd_divulgacao_grupo' => $args['cd_divulgacao_grupo'],
				  'ds_divulgacao_grupo' => '',
				  'qr_sql'              => '',
				  'cd_lista'            => '',
				  'qt_registro'         => '',
				  'usuario_inclusao'    => '',
				  'dt_inclusao'         => '',
				  'usuario_alteracao'   => '',
				  'dt_alteracao'        => '',
				  'usuario_exclusao'    => '',
				  'dt_exclusao'         => ''
                );
            }
            else
            {
                $this->divulgacao_grupo_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/divulgacao_grupo/cadastro', $data);
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
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_divulgacao_grupo'] = $this->input->post("cd_divulgacao_grupo", TRUE);   
			$args['ds_divulgacao_grupo'] = $this->input->post("ds_divulgacao_grupo", TRUE);   
			$args['qr_sql']              = $this->input->post("qr_sql", TRUE);   
			$args['cd_lista']            = $this->input->post("cd_lista", TRUE); 
			$args['cd_usuario']          = $this->session->userdata('codigo');
			
			$cd_divulgacao_grupo = $this->divulgacao_grupo_model->salvar($result, $args);
			
			redirect("ecrm/divulgacao_grupo/cadastro/".$cd_divulgacao_grupo, "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function total_registro($cd_divulgacao_grupo)
	{
		if($this->get_permissao())
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_divulgacao_grupo'] = intval($cd_divulgacao_grupo);   

			$this->divulgacao_grupo_model->total_registro($result, $args);
			
			redirect("ecrm/divulgacao_grupo/cadastro/".intval($cd_divulgacao_grupo), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
    
	function excluir($cd_divulgacao_grupo)
	{
		if($this->get_permissao())
        {			
			$this->divulgacao_grupo_model->excluir($this->session->userdata('codigo'), $cd_divulgacao_grupo);
			
			redirect("ecrm/divulgacao_grupo/index", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
}
?>