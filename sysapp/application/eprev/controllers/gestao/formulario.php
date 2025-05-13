<?php
class formulario extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('gestao/formulario_model');
    }

    private function get_permissao()
    {
        if($this->session->userdata('indic_09') == "*")
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
        {
            return TRUE;
        }
		else if($this->session->userdata('tipo') == 'G')
		{
			return TRUE;
		}
        else if($this->session->userdata('indic_13') == 'S')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_12') == '*')
        {
            return TRUE;
        }
        else if(gerencia_in(array('AI')))
        {
        	return TRUE;
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
	
	function index()
    {
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
								
			$this->load->view('gestao/formulario/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {		
		$result = null;
		$args   = Array();
		$data   = Array();
				
		$args["nr_formulario"] = $this->input->post("nr_formulario", TRUE);
		$args["ds_formulario"] = $this->input->post("ds_formulario", TRUE);
		
		manter_filtros($args);
		
		$this->formulario_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('gestao/formulario/index_result', $data);
    }
	
	function cadastro($cd_formulario = 0)
	{
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_formulario"] = $cd_formulario;
			
			if(intval($args["cd_formulario"]) == 0)
			{
				$data['row'] = array(
					'cd_formulario' => intval($cd_formulario),
					'nr_formulario' => '',
					'ds_formulario' => '',
					'fl_tipo'       => '',
					'arquivo'       => '',
					'arquivo_nome'  => ''
				);
			}
			else
			{
				$this->formulario_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/formulario/cadastro', $data);
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
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_formulario"] = $this->input->post("cd_formulario", TRUE);
			$args["nr_formulario"] = $this->input->post("nr_formulario", TRUE);
			$args["ds_formulario"] = $this->input->post("ds_formulario", TRUE);
			$args["fl_tipo"]       = $this->input->post("fl_tipo", TRUE);
			$args["arquivo"]       = $this->input->post("arquivo", TRUE);
			$args["arquivo_nome"]  = $this->input->post("arquivo_nome", TRUE);
			$args["cd_usuario"]    = $this->session->userdata('codigo');
			
			$this->formulario_model->salvar($result, $args);
			
			redirect("gestao/formulario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir($cd_formulario)
	{
		if($this->get_permissao())
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_formulario"] = $cd_formulario;
			$args["cd_usuario"]    = $this->session->userdata('codigo');
			
			$this->formulario_model->excluir($result, $args);
			
			redirect("gestao/formulario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function consulta()
    {
		$result = null;
		$args   = Array();
		$data   = Array();
							
		$this->load->view('gestao/formulario/consulta', $data);
    }
	
	function consulta_listar()
    {		
		$result = null;
		$args   = Array();
		$data   = Array();
				
		$args["nr_formulario"] = $this->input->post("nr_formulario", TRUE);
		$args["ds_formulario"] = $this->input->post("ds_formulario", TRUE);
		
		manter_filtros($args);
		
		$this->formulario_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('gestao/formulario/consulta_result', $data);
    }

    public function zip_docs()
    {

    	$result = null;
		$args   = Array();
		$data   = Array();
				
		$args["nr_formulario"] = '';
		$args["ds_formulario"] = '';
		
		manter_filtros($args);
		
		$this->formulario_model->listar($result, $args);
		$collection = $result->result_array();

		$this->load->library('zip');

		#### CRIA DIRETORIO TEMP PARA USAR O NOME ORIGINAL ####
		$dir_tmp = "../cieprev/up/cadastro_formulario_zip_2";
		if(!is_dir($dir_tmp))
		{
			mkdir($dir_tmp);
		}

		$dir = "../cieprev/up/cadastro_formulario";

		foreach ($collection as $item)
		{
			$ar_nome  = explode(".",$item['arquivo_nome']);
			$ext      = $ar_nome[ (count($ar_nome) - 1) ];

			$nome_ori = $item['nr_formulario']." - ".$item['ds_formulario_zip'].".".$ext;
			
			copy($dir."/".$item['arquivo'], $dir_tmp."/".$nome_ori);
			
			$this->zip->read_file($dir_tmp."/".$nome_ori);
			#echo $nome_ori.br();
			@unlink($dir_tmp."/".$nome_ori);
		}

		if(is_dir($dir_tmp))
		{
			//@rmdir($dir_tmp);
		}

		$this->zip->download("formulario_cadastro.zip");
    }
}
?>