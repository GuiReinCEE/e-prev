<?php

class Correspondencia_recebida_grupo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/correspondencia_recebida_grupo_model');
    }
	
	public function index()
    {
        $args = Array();
        $data = Array();
        $result = null;
				
		$this->correspondencia_recebida_grupo_model->grupo($result, $args);
		$data['arr_grupo'] = $result->result_array();

        $this->load->view('ecrm/correspondencia_recebida_grupo/index', $data);
    }
	
	public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_correspondencia_recebida_grupo'] = $this->input->post("cd_correspondencia_recebida_grupo", TRUE);   
		            
        manter_filtros($args);

        $this->correspondencia_recebida_grupo_model->listar($result, $args);
        $collection = $result->result_array();
		
		$data['collection'] = array();
		
		$i = 0;

		foreach($collection as $item)
		{
			$args['cd_correspondencia_recebida_grupo'] = $item['cd_correspondencia_recebida_grupo'];
			
			$data['collection'][$i] = $item;
			
			$this->correspondencia_recebida_grupo_model->usuario_grupo($result, $args);
			$data['collection'][$i]['usuario'] = $result->result_array();
			
			$i ++;
		}

        $this->load->view('ecrm/correspondencia_recebida_grupo/index_result', $data);
    }
	
	function cadastro($cd_correspondencia_recebida_grupo = 0)
    {
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args['cd_correspondencia_recebida_grupo'] = intval($cd_correspondencia_recebida_grupo);
			$args['cd_usuario']                        = $this->session->userdata('codigo');
			
			if ($cd_correspondencia_recebida_grupo == 0)
			{
				$data['row'] = Array(
				  'cd_correspondencia_recebida_grupo' => 0,
				  'ds_nome'                           => ''
				);
			}
			else
			{			
				$this->correspondencia_recebida_grupo_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('ecrm/correspondencia_recebida_grupo/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
	{
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args['cd_correspondencia_recebida_grupo'] = $this->input->post("cd_correspondencia_recebida_grupo", TRUE);   
			$args['ds_nome']                           = $this->input->post("ds_nome", TRUE);    		
			$args['cd_usuario']                        = $this->session->userdata('codigo');
			
			$cd_correspondencia_recebida_grupo = $this->correspondencia_recebida_grupo_model->salvar($result, $args);
			
			if(intval($args['cd_correspondencia_recebida_grupo']) == 0)
			{
				redirect("ecrm/correspondencia_recebida_grupo/usuario/".$cd_correspondencia_recebida_grupo, "refresh");
			}
			else
			{
				redirect("ecrm/correspondencia_recebida_grupo", "refresh");
			}
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function excluir($cd_correspondencia_recebida_grupo)
	{
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_correspondencia_recebida_grupo'] = $cd_correspondencia_recebida_grupo;
			$args['cd_usuario']                        = $this->session->userdata('codigo');
			
			$this->correspondencia_recebida_grupo_model->excluir($result, $args);
			
			redirect("ecrm/correspondencia_recebida_grupo", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function usuario($cd_correspondencia_recebida_grupo)
	{
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_correspondencia_recebida_grupo'] = $cd_correspondencia_recebida_grupo;

			$this->correspondencia_recebida_grupo_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->correspondencia_recebida_grupo_model->usuario_not_grupo($result, $args);
			$data['arr_usuario'] = $result->result_array();
			
			$this->correspondencia_recebida_grupo_model->usuario_grupo($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('ecrm/correspondencia_recebida_grupo/usuario', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar_usuario()
	{
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_correspondencia_recebida_grupo'] = $this->input->post("cd_correspondencia_recebida_grupo", TRUE);   
			$args['cd_usuario']                        = $this->input->post("cd_usuario", TRUE);    		
			$args['cd_usuario_inclusao']               = $this->session->userdata('codigo');
			
			$this->correspondencia_recebida_grupo_model->salvar_usuario($result, $args);
			
			redirect("ecrm/correspondencia_recebida_grupo/usuario/".$args['cd_correspondencia_recebida_grupo'], "refresh");	
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function excluir_usuario($cd_correspondencia_recebida_grupo, $cd_correspondencia_recebida_grupo_usuario)
	{
		if(gerencia_in(array('GFC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_correspondencia_recebida_grupo']         = $cd_correspondencia_recebida_grupo;
			$args['cd_correspondencia_recebida_grupo_usuario'] = $cd_correspondencia_recebida_grupo_usuario;
			$args['cd_usuario']                                = $this->session->userdata('codigo');
			
			$this->correspondencia_recebida_grupo_model->excluir_usuario($result, $args);
			
			redirect("ecrm/correspondencia_recebida_grupo/usuario/".intval($cd_correspondencia_recebida_grupo), "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}

?>