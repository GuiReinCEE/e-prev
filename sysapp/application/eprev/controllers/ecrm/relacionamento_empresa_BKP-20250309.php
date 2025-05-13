<?php
class relacionamento_empresa extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		$this->load->model('expansao/empresa_model');
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GRI', 'GCM')))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }
	public function os61045()
	{
		echo 'INCIANDO'.br();
		
		$csv = './up/os61045/os61045.csv';
		
		if(file_exists($csv))
		{
			$arquivo = fopen ($csv, 'r');
			$result = array();
			
			while(!feof($arquivo)){
				$result[] = explode(";",fgets($arquivo));
			}
			
			fclose($arquivo);
			
			$d = array();
			
			foreach($result as $key => $item)
			{
				if(isset($item[1]))
				{
					$ini = 8;
					$fim = count($item) - 1;
					
					$ds_empresa = str_replace('"', "", $item[1]);
					$ds_empresa = str_replace("'", "", $ds_empresa);
					
					$telefone = str_replace('"', "", $item[5]);
					$telefone = str_replace("'", "", $telefone);
					
					$d[$key] = array(
						'cd_empresa'     => 0,
						'ds_empresa'     => $ds_empresa,
						'uf'             => '',
						'cidade'         => str_replace('"', "", $item[6]),
						'cep'            => '',
						'logradouro'     => '',
						'numero'         => '',
						'complemento'    => '',
						'bairro'         => '',
						'telefone'       => $telefone,
						'telefone_ramal' => '',
						'fax'            => '',
						'fax_ramal'      => '',
						'celular'        => '',
						'site'           => '',
						'nr_colaborador' => '',
						'email'          => array(),
						'cd_usuario'     => 251
					);
					
					while($ini <= $fim)
					{
						if (strstr($item[$ini], '@')) 
						{
							$d[$key]['email'][] = str_replace('"', "", $item[$ini]);
						}
						
						$ini ++;
					}
				}
					
			}
			
			$data = Array();
			$result = null;
			
			foreach($d as $key => $item)
			{
				$args = $item;
				
				$cd_empresa = $this->empresa_model->salvar( $result, $args );
			
				if(count($item['email']) > 0)
				{
					foreach($item['email'] as $key2 => $item2)
					{
						if(trim($item2) != '')
						{
							$args["cd_empresa"]  = $cd_empresa;
							$args["ds_email"]    = $item2;
							$args["cd_usuario"]  = 251;
							
							$this->empresa_model->salvar_email( $result, $args );
						}
					
					}
				}
				
				$args["cd_empresa"]                   = $cd_empresa;
				$args["cd_empresa_contato"]           = 0;
				$args["dt_contato"]                   = '17/12/2024';
				$args["cd_empresa_contato_atividade"] = 1;
				$args["ds_contato"]                   = utf8_encode('Inclusão de Empresa solicitado via OS 61045');
				$args["cd_empresa_origem_contato"]    = 1;
				$args["cd_usuario"]                   = 251;
				
				$this->empresa_model->salvar_contato( $result, $args );
			}
		}
		else
		{
			echo 'ERRO';
		}	
	}
	
	public function importa_os87320()
	{
		$collection = $this->empresa_model->lista_temp_os87320();
		
		echo '<pre>';
		
		$args = Array();
        $data = Array();
        $result = null;
		
		foreach($collection as $item)
		{			
			$telefone_1 = $item['telefone_1'];
			$telefone_1 = str_replace('"', "", $telefone_1);
			$telefone_1 = str_replace(' ', "", $telefone_1);
			
			$telefone_2 = $item['telefone_2'];
			$telefone_2 = str_replace('"', "", $telefone_2);
			$telefone_2 = str_replace(' ', "", $telefone_2);
		
			$args["cd_empresa"]     = 0;
			$args["ds_empresa"]     = str_replace('"', "", $item['nome']);
			$args["uf"]             = str_replace('"', "", $item['uf']);
			$args["cidade"]         = str_replace('"', "", $item['cidade']);
			$args["cep"]            = '';
			$args["logradouro"]     = str_replace('"', "", $item['endereco']);
			$args["numero"]         = '';
			$args["complemento"]    = '';
			$args["bairro"]         = '';
			$args["telefone"]       = $telefone_1;
			$args["telefone_ramal"] = '';
			$args["fax"]            = '';
			$args["fax_ramal"]      = '';
			$args["celular"]        = $telefone_2;
			$args["site"]           = '';
			$args["nr_colaborador"] = '';
			$args["cd_usuario"]     = 251;
			
			$cd_empresa = $this->empresa_model->salvar( $result, $args );
			
			if(trim($item['email_1']) != '')
			{
				$args["cd_empresa"]  = $cd_empresa;
				$args["ds_email"]    = str_replace('"', "", $item['email_1']);
				$args["cd_usuario"]  = 251;
				
				$this->empresa_model->salvar_email( $result, $args );
			}
			
			if(trim($item['email_2']) != '')
			{
				$args["cd_empresa"]  = $cd_empresa;
				$args["ds_email"]    = str_replace('"', "", $item['email_2']);
				$args["cd_usuario"]  = 251;
				
				$this->empresa_model->salvar_email( $result, $args );
			}
			
			$args["cd_empresa"]                   = $cd_empresa;
			$args["cd_empresa_contato"]           = 0;
			$args["dt_contato"]                   = '27/08/2024';
			$args["cd_empresa_contato_atividade"] = 1;
			$args["ds_contato"]                   = utf8_encode('Inclusão de Empresa solicitado via OS 87320');
			$args["cd_empresa_origem_contato"]    = 1;
			$args["cd_usuario"]                   = 251;
			
			$this->empresa_model->salvar_contato( $result, $args );
			
			$this->empresa_model->set_temp_os87320($item['cd_os87320'], $cd_empresa);
			
			echo $cd_empresa;
			echo '<br/>';
			print_r($item);

		}
		
		
	}

    function index()
    {
		if ($this->get_permissao())
        {		
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->empresa_model->uf($result, $args);
			$data['arr_uf'] = $result->result_array();
			
			$this->empresa_model->grupos($result, $args);
			$data['arr_grupo'] = $result->result_array();
			
			$this->empresa_model->segmentos($result, $args);
			$data['arr_segmento'] = $result->result_array();
			
			$this->empresa_model->evento($result, $args);
			$data['arr_evento'] = $result->result_array();
			
			$this->empresa_model->origem($result, $args);
			$data['arr_origem'] = $result->result_array();

			$this->load->view('ecrm/relacionamento_empresa/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }
	
	function cidades()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['uf']        = $this->input->post('uf', true);
		$args['fl_filtro'] = $this->input->post('fl_filtro', true);
		
		$this->empresa_model->cidades($result, $args);
        $arr = $result->result_array();
				
	    echo json_encode($arr);
	}	
	
	function listar()
    {
		$args = Array();
        $data = Array();
        $result = null;

		$args["ds_empresa"]        = $this->input->post("ds_empresa", TRUE);
		$args["uf"]                = $this->input->post("uf", TRUE);
		$args["cidade"]            = $this->input->post("cidade", TRUE);
		$args["fl_nr_colaborador"] = $this->input->post("fl_nr_colaborador", TRUE);
		$args["fl_contato"]        = $this->input->post("fl_contato", TRUE);
		$args["grupos"]            = $this->input->post("grupos");
		$args["segmentos"]         = $this->input->post("segmentos");
		$args["evento"]            = $this->input->post("evento");
		$args["origem"]            = $this->input->post("origem");
		$args["fl_exibe"]          = $this->input->post("fl_exibe");
		
		#print_r($args);

		$this->empresa_model->listar( $result, $args );
		$collection = $result->result_array();
		for($i = 0; $i < count($collection); $i++)
		{
			$args['cd_empresa'] = $collection[$i]['cd_empresa'];
		
			$this->empresa_model->grupos_empresa( $result, $args );
			$collection[$i]['grupos'] = $result->result_array();
			
			$this->empresa_model->segmentos_empresa( $result, $args );
			$collection[$i]['segmentos'] = $result->result_array();
			
			$this->empresa_model->listar_evento( $result, $args );
			$collection[$i]['arr_evento'] = $result->result_array();
		}
		
		$data['collection'] = $collection;
		
		if(trim($args["fl_exibe"]) == "M")
		{
			$this->load->view('ecrm/relacionamento_empresa/partial_result_mapa', $data);
		}
		else
		{

			$this->load->view('ecrm/relacionamento_empresa/partial_result', $data);
		}
    }

	function cadastro($cd_empresa = 0)
	{
		if ($this->get_permissao())
        {		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_empresa'] = $cd_empresa;
			
			$this->empresa_model->uf($result, $args);
			$data['arr_uf'] = $result->result_array();
			
			if ($cd_empresa == 0)
			{
				$data['row'] = Array(
					'cd_empresa'     => 0,
					'ds_empresa'     => '',
					'fax'            => '',
					'fax_ramal'      => '',
					'telefone'       => '',
					'telefone_ramal' => '',
					'celular'        => '',
					'cep'            => '',
					'uf'             => '',
					'cidade'         => '',
					'logradouro'     => '',
					'numero'         => '',
					'complemento'    => '',
					'bairro'         => '',
					'site'           => '',
					'nr_colaborador' => ''
				);
			}
			else
			{
				$this->empresa_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view( 'ecrm/relacionamento_empresa/cadastro', $data );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}

	function salvar()
	{
		$args = Array();
        $data = Array();
        $result = null;
	
		$args["cd_empresa"]     = $this->input->post("cd_empresa", TRUE);
		$args["ds_empresa"]     = $this->input->post("ds_empresa", TRUE);
		$args["uf"]             = $this->input->post("uf", TRUE);
		$args["cidade"]         = $this->input->post("cidade", TRUE);
		$args["cep"]            = $this->input->post("cep", TRUE);
		$args["logradouro"]     = $this->input->post("logradouro", TRUE);
		$args["numero"]         = $this->input->post("numero", TRUE);
		$args["complemento"]    = $this->input->post("complemento", TRUE);
		$args["bairro"]         = $this->input->post("bairro", TRUE);
		$args["telefone"]       = $this->input->post("telefone", TRUE);
		$args["telefone_ramal"] = $this->input->post("telefone_ramal", TRUE);
		$args["fax"]            = $this->input->post("fax", TRUE);
		$args["fax_ramal"]      = $this->input->post("fax_ramal", TRUE);
		$args["celular"]        = $this->input->post("celular", TRUE);
		$args["site"]           = $this->input->post("site", TRUE);
		$args["nr_colaborador"] = $this->input->post("nr_colaborador", TRUE);
		$args["cd_usuario"]     = $this->session->userdata('codigo');

		$cd_empresa = $this->empresa_model->salvar( $result, $args );

		redirect( "ecrm/relacionamento_empresa/cadastro/".intval($cd_empresa), "refresh" );
	}
	
	function excluir($cd_empresa)
	{
		$args = Array();
        $data = Array();
        $result = null;
	
		$args["cd_empresa"] = $cd_empresa;
		$args["cd_usuario"] = $this->session->userdata('codigo');
	
		$this->empresa_model->excluir( $result, $args );

		redirect( "ecrm/relacionamento_empresa/", "refresh" );
	}
	
	function listar_emails()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		
		$this->empresa_model->listar_emails( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/emails_result', $data);
	}
	
	function salvar_email()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]  = $this->input->post("cd_empresa",TRUE);
		$args["ds_email"]    = $this->input->post("ds_email",TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$this->empresa_model->salvar_email( $result, $args );
	}
	
	function excluir_email()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa_email"] = $this->input->post("cd_empresa_email",TRUE);
		$args["cd_usuario"]       = $this->session->userdata('codigo');
		
		$this->empresa_model->excluir_email( $result, $args );
	}
	
	function listar_grupos()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		
		$this->empresa_model->listar_grupos( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/grupos_result', $data);
	}
	
	function salvar_grupo()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]  = $this->input->post("cd_empresa",TRUE);
		$args["cd_grupo"]    = $this->input->post("cd_grupo",TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$this->empresa_model->salvar_grupo( $result, $args );
	}
	
	function excluir_grupo()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa_grupo_relaciona"] = $this->input->post("cd_empresa_grupo_relaciona",TRUE);
		$args["cd_usuario"]                 = $this->session->userdata('codigo');
		
		$this->empresa_model->excluir_grupo( $result, $args );
	}
	
	function listar_segmentos()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		
		$this->empresa_model->listar_segmentos( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/segmentos_result', $data);
	}
	
	function salvar_segmento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]  = $this->input->post("cd_empresa",TRUE);
		$args["cd_segmento"] = $this->input->post("cd_segmento",TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		$this->empresa_model->salvar_segmento( $result, $args );
	}
	
	function excluir_segmento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa_segmento_relaciona"] = $this->input->post("cd_empresa_segmento_relaciona",TRUE);
		$args["cd_usuario"]                    = $this->session->userdata('codigo');
		 
		$this->empresa_model->excluir_segmento( $result, $args );
	}
	
	function contato($cd_empresa = 0, $cd_empresa_contato = 0)
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$args['cd_empresa']         = $cd_empresa;
		$args['cd_empresa_contato'] = $cd_empresa_contato;
		
		if(intval($args['cd_empresa_contato']) == 0)
		{
			$data['row'] = array(
				'cd_empresa'                   => intval($args['cd_empresa']),
				'cd_empresa_contato'           => intval($args['cd_empresa_contato']),
				'dt_contato'                   => '',
				'cd_empresa_contato_atividade' => '',
				'cd_empresa_origem_contato'    => '',
				'ds_contato'                   => ''
			);
		}
		else
		{
			$this->empresa_model->carrega_contato($result, $args);
			$data['row'] = $result->row_array();
		}
		
		$this->load->view("ecrm/relacionamento_empresa/contato", $data);
	}
	
	function listar_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		
		$data['collection'] = array();
		
		$this->empresa_model->listar_contato( $result, $args );
		$arr = $result->result_array();
		
		$i = 0;
		
		foreach($arr as $item)
		{
			$args['cd_empresa_contato'] = intval($item['cd_empresa_contato']);
			
			$data['collection'][$i] = $item;
			
			$this->empresa_model->listar_contato_anexo( $result, $args );
			$data['collection'][$i]['arr_anexo'] = $result->result_array();
			
			$i++;
		}
		
		$this->load->view('ecrm/relacionamento_empresa/contatos_result', $data);
	}
	
	function salvar_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]                   = $this->input->post("cd_empresa",TRUE);
		$args["cd_empresa_contato"]           = $this->input->post("cd_empresa_contato",TRUE);
		$args["dt_contato"]                   = $this->input->post("dt_contato",TRUE);
		$args["cd_empresa_contato_atividade"] = $this->input->post("cd_empresa_contato_atividade",TRUE);
		$args["ds_contato"]                   = utf8_encode($this->input->post("ds_contato",TRUE));
		$args["cd_empresa_origem_contato"]    = $this->input->post("cd_empresa_origem_contato",TRUE);
		$args["cd_usuario"]                   = $this->session->userdata('codigo');
		
		$this->empresa_model->salvar_contato( $result, $args );
		
		redirect("ecrm/relacionamento_empresa/contato/".$args["cd_empresa"], "refresh" );
	}
	
	function excluir_contato()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa_contato"] = $this->input->post("cd_empresa_contato",TRUE);
		$args["cd_usuario"]         = $this->session->userdata('codigo');
		
		$this->empresa_model->excluir_contato( $result, $args );
	}
	
	function pessoas($cd_empresa)
	{
		$args = Array();
		$data = Array();
		$result = null;

		$data['cd_empresa'] = intval($cd_empresa);
		$this->load->view("ecrm/relacionamento_empresa/pessoas", $data);
	}
	
	function pessoasListar()
	{
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_empresa']    = $this->input->post("cd_empresa",TRUE);
		$data['cd_empresa']    = $this->input->post("cd_empresa",TRUE);
		$data['fl_count_grid'] = $this->input->post("fl_count_grid",TRUE);
		
		$this->empresa_model->listar_pessoas( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view("ecrm/relacionamento_empresa/pessoas_result", $data);
	}	

	function anexo($cd_empresa)
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$data['cd_empresa'] = $cd_empresa;	
				
		$this->load->view('ecrm/relacionamento_empresa/anexo', $data);
	}
	
	function listar_anexos()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_empresa'] = $this->input->post("cd_empresa", TRUE);
		
		$this->empresa_model->listar_anexos($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/anexo_result', $data);
	}
	
	function salvar_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome'] = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']      = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				$args['cd_empresa']   = $this->input->post("cd_empresa", TRUE);
				$args["cd_usuario"]   = $this->session->userdata('codigo');
				
				$this->empresa_model->salvar_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("ecrm/relacionamento_empresa/anexo/".intval($args["cd_empresa"]), "refresh");
	}
	
	function excluir_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_empresa_anexo'] = $this->input->post("cd_empresa_anexo", TRUE);
		$args["cd_usuario"]       = $this->session->userdata('codigo');

		$this->empresa_model->excluir_anexo($result, $args);
	}
	
	function contato_anexo($cd_empresa, $cd_empresa_contato)
	{
		$args = Array();
		$data = Array();
		$result = null;
	
		$data['cd_empresa']         = $cd_empresa;	
		$data['cd_empresa_contato'] = $cd_empresa_contato;	
				
		$this->load->view('ecrm/relacionamento_empresa/contato_anexo', $data);
	}
	
	function salvar_contato_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome']       = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']            = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				$args['cd_empresa']         = $this->input->post("cd_empresa", TRUE);
				$args["cd_empresa_contato"] = $this->input->post("cd_empresa_contato", TRUE);
				$args["cd_usuario"]         = $this->session->userdata('codigo');
				
				$this->empresa_model->salvar_contato_anexo($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("ecrm/relacionamento_empresa/contato_anexo/".intval($args["cd_empresa"])."/".$args["cd_empresa_contato"], "refresh");
	}
	
	function listar_contato_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_empresa_contato'] = $this->input->post("cd_empresa_contato", TRUE);
		
		$this->empresa_model->listar_contato_anexo($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/contato_anexo_result', $data);
	}
	
	function excluir_contato_anexo()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_empresa_contato_anexo'] = $this->input->post("cd_empresa_contato_anexo", TRUE);
		$args["cd_usuario"]               = $this->session->userdata('codigo');

		$this->empresa_model->excluir_contato_anexo($result, $args);
	}
	
	function salvar_evento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]        = $this->input->post("cd_empresa",TRUE);
		$args["cd_empresa_evento"] = $this->input->post("cd_empresa_evento",TRUE);
		$args["cd_usuario"]        = $this->session->userdata('codigo');
		
		$this->empresa_model->salvar_evento( $result, $args );
	}
	
	function listar_evento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		
		$this->empresa_model->listar_evento( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/relacionamento_empresa/evento_result', $data);
	}
	
	function excluir_evento()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa_evento_relaciona"] = $this->input->post("cd_empresa_evento_relaciona",TRUE);
		$args["cd_usuario"]                  = $this->session->userdata('codigo');
		 
		$this->empresa_model->excluir_evento( $result, $args );
	}
	
	function relatorio()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$this->empresa_model->atividade($result, $args);
		$data['arr_atividade'] = $result->result_array();
		
		$this->empresa_model->empresa_contato($result, $args);
		$data['arr_empresa'] = $result->result_array();

		$this->load->view('ecrm/relacionamento_empresa/relatorio', $data);
	}
	
	function listar_relatorio()
	{
		$args = Array();
        $data = Array();
        $result = null;

		$args["ds_empresa"]                   = $this->input->post("ds_empresa", TRUE);
		$args["cd_empresa"]                   = $this->input->post("cd_empresa", TRUE);
		$args["dt_ini"]                       = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"]                       = $this->input->post("dt_fim", TRUE);
		$args["cd_empresa_contato_atividade"] = $this->input->post("cd_empresa_contato_atividade", TRUE);

		manter_filtros($args);

		$this->empresa_model->listar_relatorio( $result, $args );
		$data['collection'] = $result->result_array();
		
        $this->load->view('ecrm/relacionamento_empresa/relatorio_result', $data);
	}
	
	function agenda($cd_empresa, $cd_empresa_agenda = 0)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_empresa']        = $cd_empresa;
		$args['cd_empresa_agenda'] = $cd_empresa_agenda;
		
		$this->empresa_model->carrega($result, $args);
		$data['emp'] = $result->row_array();
		
		if(intval($args['cd_empresa_agenda']) == 0)
		{
			$this->empresa_model->listar_emails( $result, $args );
			$emails = $result->result_array();

			$ds_email_encaminhar = '';

			foreach ($emails as $key => $item) 
			{
				$ds_email_encaminhar .= (trim($ds_email_encaminhar) != '' ? ';' : '').$item['ds_email'];
			}

			$data['row'] = array(
				'cd_empresa'        => $cd_empresa,
				'cd_empresa_agenda' => 0,
				'ds_empresa_agenda' => '',
				'local'             => '',
				'dt_inicio'         => '',
				'hr_inicio'         => '',
				'dt_final'          => '',
				'hr_final'          => '',
				'ds_email_envia'    => '',
				'ds_email_encaminhar' => $ds_email_encaminhar
			);
		}
		else
		{
			$this->empresa_model->carrega_agenda($result, $args);
			$data['row'] = $result->row_array();
		}
		
		$this->empresa_model->listar_agenda( $result, $args );
		$data['collection'] = $result->result_array();

		$data['email_envia'] = array(
			array('value' => 'gepj@familiaprevidencia.com.br', 'text' => 'gepj@familiaprevidencia.com.br'),
			array('value' => 'gestaomunicipios@familiaprevidencia.com.br', 'text' => 'gestaomunicipios@familiaprevidencia.com.br'),
		);
		
		$this->load->view('ecrm/relacionamento_empresa/agenda', $data);
	}
	
	function salvar_agenda()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]        = $this->input->post("cd_empresa",TRUE);
		$args["cd_empresa_agenda"] = $this->input->post("cd_empresa_agenda",TRUE);
		$args["ds_empresa_agenda"] = $this->input->post("ds_empresa_agenda",TRUE);
		$args["local"]             = $this->input->post("local",TRUE);
		$args["ds_email_envia"]    = $this->input->post("ds_email_envia",TRUE);
		$args["ds_email_encaminhar"]    = $this->input->post("ds_email_encaminhar",TRUE);
		$args["dt_inicio"]         = $this->input->post("dt_inicio",TRUE).' '.$this->input->post("hr_inicio",TRUE);
		$args["dt_final"]          = $this->input->post("dt_final",TRUE).' '.$this->input->post("hr_final",TRUE);
		$args["cd_usuario"]        = $this->session->userdata('codigo');

		$args['ds_texto_agenda'] = $this->input->post("ds_empresa_agenda",TRUE).chr(13).chr(10);

		if(intval($args["cd_empresa_agenda"]) > 0)
		{
			$arq = $this->empresa_model->get_arquivos($args["cd_empresa_agenda"]);

			foreach ($arq as $key => $item) 
			{
				$args['ds_texto_agenda'] .= '<a href="'.base_url().'up/relacionamento_empresa/'.$item['arquivo'].'">'.$item['arquivo_nome'].'</a>'.chr(13).chr(10);
			}
		}
		
		$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

		$args['arquivo'] = array();

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {     
                $args['arquivo'][$nr_conta]['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                $args['arquivo'][$nr_conta]['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);  
    
                $args['ds_texto_agenda'] .= '<a href="'.base_url().'up/relacionamento_empresa/'.$args['arquivo'][$nr_conta]['arquivo'].'">'.$args['arquivo'][$nr_conta]['arquivo_nome'].'</a>'.chr(13).chr(10);

                $nr_conta++;
            }
        }
	
		$this->empresa_model->salvar_agenda( $result, $args );
		
		redirect("ecrm/relacionamento_empresa/agenda/".$args["cd_empresa"], "refresh" );
	}
	
	function excluir_agenda($cd_empresa, $cd_empresa_agenda)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_empresa"]        = $cd_empresa;
		$args["cd_empresa_agenda"] = $cd_empresa_agenda;
		$args["cd_usuario"]        = $this->session->userdata('codigo');
	
		$this->empresa_model->excluir_agenda( $result, $args );
		
		redirect("ecrm/relacionamento_empresa/agenda/".$cd_empresa, "refresh" );
	}
	
	function mapaCidade()
    {
		$result = null;
		$data   = array();
		$args   = array();

		$args["ar_cidade"] = $this->input->post("ar_cidade", TRUE);
		$args["ar_uf"]     = $this->input->post("ar_uf", TRUE);
		
		#### MAPA CIDADE ####
		$this->empresa_model->mapaCidade($result, $args);
		$ar_mapa_cidade = $result->result_array();	

		$ar_json  = array();
		$nr_conta = 1;
		foreach($ar_mapa_cidade as $ar_reg)
		{
			$ar_json[] = array("id" => $nr_conta, "ds_cidade" => utf8_encode($ar_reg["ds_cidade"]), "latitude" => $ar_reg["latitude"], "longitude" => $ar_reg["longitude"]);
			$nr_conta++;
		}			
		
		echo json_encode($ar_json);exit;		
    }	
}
?>