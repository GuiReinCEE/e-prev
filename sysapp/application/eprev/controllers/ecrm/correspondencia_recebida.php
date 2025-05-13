<?php

class correspondencia_recebida extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/correspondencia_recebida_model');
    }
	
	public function index()
    {
        $args = Array();
        $data = Array();
        $result = null;
		
		$this->correspondencia_recebida_model->gerencia($result, $args);
        $data['arr_gerencia'] = $result->result_array();
		
		$this->correspondencia_recebida_model->grupo($result, $args);
		$data['arr_grupo'] = $result->result_array();

        $this->load->view('ecrm/correspondencia_recebida/index', $data);
    }
	
	public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['nr_numero']                         = $this->input->post("nr_numero", TRUE);    
        $args['nr_ano']                            = $this->input->post("nr_ano", TRUE);    
        $args['cd_gerencia_destino']               = $this->input->post("cd_gerencia_destino", TRUE);   
        $args['cd_correspondencia_recebida_grupo'] = $this->input->post("cd_correspondencia_recebida_grupo", TRUE);   
        $args['fl_status']       				   = $this->input->post("fl_status", TRUE);   
        $args['dt_inclusao_ini']  				   = $this->input->post("dt_inclusao_ini", TRUE);   
        $args['dt_inclusao_fim'] 				   = $this->input->post("dt_inclusao_fim", TRUE);   
        $args['dt_envio_ini']     				   = $this->input->post("dt_envio_ini", TRUE);   
        $args['dt_envio_fim']     				   = $this->input->post("dt_envio_fim", TRUE);   
		$args['cd_gerencia']       				   = $this->session->userdata('divisao');
		            
        manter_filtros($args);

        $this->correspondencia_recebida_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/correspondencia_recebida/index_result', $data);
    }
	
	public function relatorio()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$this->correspondencia_recebida_model->gerencia($result, $args);
        $data['arr_gerencia'] = $result->result_array();
		
		$this->correspondencia_recebida_model->usuarios_envio($result, $args);
        $data['arr_usuarios_envio'] = $result->result_array();
		
		$this->correspondencia_recebida_model->usuarios_recebido($result, $args);
        $data['arr_usuarios_recebido'] = $result->result_array();
		
		$this->correspondencia_recebida_model->tipos($result, $args);
		$data['arr_tipos'] = $result->result_array();
		
		$this->correspondencia_recebida_model->grupo($result, $args);
		$data['arr_grupo'] = $result->result_array();

        $this->load->view('ecrm/correspondencia_recebida/relatorio', $data);
	}
	
	function listar_relatorio()
	{
		$args = Array();
        $data = Array();
        $result = null;
				
		$args['nr_numero']                        = $this->input->post("nr_numero", TRUE);    
        $args['nr_ano']                           = $this->input->post("nr_ano", TRUE);    
        $args['cd_gerencia_destino']              = $this->input->post("cd_gerencia_destino", TRUE);   
        $args['fl_status']                        = $this->input->post("fl_status", TRUE);   
        $args['dt_envio_ini']                     = $this->input->post("dt_envio_ini", TRUE);   
        $args['dt_envio_fim']                     = $this->input->post("dt_envio_fim", TRUE);   
        $args['dt_recebido_ini']                  = $this->input->post("dt_recebido_ini", TRUE);   
        $args['dt_recebido_fim']                  = $this->input->post("dt_recebido_fim", TRUE);   
        $args['cd_usuario_envio']                 = $this->input->post("cd_usuario_envio", TRUE);   
        $args['cd_usuario_recebido']              = $this->input->post("cd_usuario_recebido", TRUE);   
        $args['cd_empresa']                       = $this->input->post("cd_empresa", TRUE);   
        $args['cd_registro_empregado']            = $this->input->post("cd_registro_empregado", TRUE);   
        $args['seq_dependencia']                  = $this->input->post("seq_dependencia", TRUE);   
        $args['nome_participante']                = $this->input->post("nome_participante", TRUE);  
        $args['identificador']                    = $this->input->post("identificador", TRUE);  
        $args['cd_correspondencia_recebida_tipo'] = $this->input->post("cd_correspondencia_recebida_tipo", TRUE);  
        $args['fl_recebido']                      = $this->input->post("fl_recebido", TRUE);  
        $args['fl_recusado']                      = $this->input->post("fl_recusado", TRUE);  
		$args['cd_gerencia_destino']              = $this->input->post("cd_gerencia_destino", TRUE);   

		manter_filtros($args);

        $this->correspondencia_recebida_model->listar_relatorio($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/correspondencia_recebida/relatorio_result', $data);		
	}
	
	function cadastro($cd_correspondencia_recebida = 0)
    {
		if((gerencia_in(array('GFC'))) OR ($cd_correspondencia_recebida > 0) OR ($this->session->userdata('codigo') == 251))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args['cd_correspondencia_recebida'] = intval($cd_correspondencia_recebida);
			
			$this->correspondencia_recebida_model->gerencia($result, $args);
			$data['arr_gerencia'] = $result->result_array();
			
			$this->correspondencia_recebida_model->grupo($result, $args);
			$data['arr_grupo'] = $result->result_array();
			
			if ($cd_correspondencia_recebida == 0)
			{
				$data['row'] = Array(
				  'cd_correspondencia_recebida'       => 0,
				  'ano_numero'                        => '',
				  'dt_envio'                          => '',
				  'cd_gerencia_destino'               => '',
				  'dt_inclusao'                       => '',
				  'usuario_cadastro'                  => '',
				  'usuario_envio'                     => '',
				  'cd_correspondencia_recebida_grupo' => ''
				);
			}
			else
			{
				$this->correspondencia_recebida_model->tipos($result, $args);
				$data['arr_tipos'] = $result->result_array();
			
				$this->correspondencia_recebida_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('ecrm/correspondencia_recebida/cadastro', $data);
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
		
		$args['cd_correspondencia_recebida']       = $this->input->post("cd_correspondencia_recebida", TRUE);    
		$args['cd_gerencia_destino']               = $this->input->post("cd_gerencia_destino", TRUE);    
		$args['cd_correspondencia_recebida_grupo'] = $this->input->post("cd_correspondencia_recebida_grupo", TRUE);    
		$args['cd_usuario']                        = $this->session->userdata('codigo');
		
		$cd_correspondencia_recebida = $this->correspondencia_recebida_model->salvar($result, $args);
		
		redirect("ecrm/correspondencia_recebida/cadastro/".$cd_correspondencia_recebida, "refresh");
	}
	
	function excluir($cd_correspondencia_recebida)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $cd_correspondencia_recebida;
		$args['cd_usuario']                  = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->excluir($result, $args);
		
		redirect("ecrm/correspondencia_recebida", "refresh");	
	}
	
	function salvar_item()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida']      = $this->input->post("cd_correspondencia_recebida", TRUE);    
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE);    
		$args['dt_correspondencia']               = $this->input->post("dt_correspondencia", TRUE);    
		$args['hr_correspondencia']               = $this->input->post("hr_correspondencia", TRUE);    
		$args['cd_correspondencia_recebida_tipo'] = $this->input->post("cd_correspondencia_recebida_tipo", TRUE);    
		$args['origem']                           = utf8_decode($this->input->post("origem", TRUE));    
		$args['identificador']                    = utf8_decode($this->input->post("identificador", TRUE));    
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->salvar_item($result, $args);
		
		redirect("ecrm/correspondencia_recebida/cadastro/".$args['cd_correspondencia_recebida'], "refresh");
	}
	
	function listar_itens()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $this->input->post("cd_correspondencia_recebida", TRUE);    
		$data['dt_envio']                    = $this->input->post("dt_envio", TRUE);    
		
		$this->correspondencia_recebida_model->lista_itens($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/correspondencia_recebida/itens_result', $data);
	}
	
	function excluir_item()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida']      = $this->input->post("cd_correspondencia_recebida", TRUE);    
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE);    
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->excluir_item($result, $args);
	}
	
	function carrega_item()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE); 
		
		$this->correspondencia_recebida_model->carrega_item($result, $args);
		$row = $result->row_array();
		
		$row = array_map("arrayToUTF8", $row);			
		
	    echo json_encode($row);
	}
	
	function enviar($cd_correspondencia_recebida)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $cd_correspondencia_recebida; 
		$args['cd_usuario']                  = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->enviar($result, $args);
		
		redirect("ecrm/correspondencia_recebida/cadastro/".$cd_correspondencia_recebida, "refresh");
	}
	
	function receber($cd_correspondencia_recebida)
	{	
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $cd_correspondencia_recebida;
		$args['cd_usuario']                  = $this->session->userdata('codigo');
		 
		$this->correspondencia_recebida_model->grupo_destino($result, $args);
		$row = $result->row_array(); 
		 
		$this->correspondencia_recebida_model->carrega($result, $args);
		$data['row'] = $result->row_array();
		
		if($data['row']['dt_envio'] != '')
		{
			if(($this->session->userdata('divisao') == 'GFC') OR ($data['row']['cd_gerencia_destino'] == $this->session->userdata('divisao')) OR ($this->session->userdata('codigo') == 251) OR (intval($row['tl']) > 0) OR ($data['row']['cd_gerencia_destino'] == $this->session->userdata('divisao_ant')))
			{
				$this->correspondencia_recebida_model->correspondencia_recebidas($result, $args);
				$row = $result->row_array();
			
				$data['tl_recebido'] = $row['tl'];
			
				$this->load->view('ecrm/correspondencia_recebida/receber', $data);
			}
			else
			{
				exibir_mensagem("SUA GERÊNCIA NÃO É A MESMA DA GERÊNCIA DE DESTINO DO PROTOCOLO");
			}
		}
		else
		{
			exibir_mensagem("PROTOCOLO NÃO FOI ENVIADO");
		}
	}
	
	function listar_itens_receber()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $this->input->post("cd_correspondencia_recebida", TRUE); 
		
		$this->correspondencia_recebida_model->lista_itens($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/correspondencia_recebida/itens_receber_result', $data);
	}
	
	function salvar_re()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE); 
		$args['cd_empresa']                       = $this->input->post("cd_empresa", TRUE); 
		$args['cd_registro_empregado']            = $this->input->post("cd_registro_empregado", TRUE); 
		$args['seq_dependencia']                  = $this->input->post("seq_dependencia", TRUE);
		
		$this->correspondencia_recebida_model->salvar_re($result, $args);
	}
	
	function receber_correspondencia()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida']      = $this->input->post("cd_correspondencia_recebida", TRUE); 
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE); 
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->receber_correspondencia($result, $args);
		
		$this->correspondencia_recebida_model->correspondencia_recebidas($result, $args);
		$row = $result->row_array();
	
		echo $row['tl'];
	}
	
	function receber_todas_correspondencia()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida'] = $this->input->post("cd_correspondencia_recebida", TRUE); 
		$args['cd_usuario']                  = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->receber_todas_correspondencia($result, $args);
	}
	
	function recusar($cd_correspondencia_recebida, $cd_correspondencia_recebida_item)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['cd_correspondencia_recebida']      = $cd_correspondencia_recebida; 
		$data['cd_correspondencia_recebida_item'] = $cd_correspondencia_recebida_item; 
		
		$this->load->view('ecrm/correspondencia_recebida/recusar', $data);
	}
	
	function recusar_correspondecia()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida']      = $this->input->post("cd_correspondencia_recebida", TRUE); 
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE); 
		$args['motivo_recusa']                    = $this->input->post("motivo_recusa", TRUE); 
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->recusar_correspondecia($result, $args);
		
		$this->correspondencia_recebida_model->correspondencia_recebidas($result, $args);
		$row = $result->row_array();
	
		if(intval($row['tl']) == 0)
		{
			$this->correspondencia_recebida_model->receber_todas_correspondencia($result, $args);
		}
			
		redirect("ecrm/correspondencia_recebida/receber/".$args['cd_correspondencia_recebida'], "refresh");
	}
	
	function recusar_ok()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida_item'] = $this->input->post("cd_correspondencia_recebida_item", TRUE); 
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		$this->correspondencia_recebida_model->recusar_ok($result, $args);
	}
	
	function correspondecia_items_recusados()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_correspondencia_recebida']       = 0;    
		$args['cd_gerencia_destino']               = '';
		$args['cd_correspondencia_recebida_grupo'] = '';
		$args['cd_usuario']                        = $this->session->userdata('codigo');
		
		$arr = $this->input->post("itens", TRUE); 
		
		$args['cd_correspondencia_recebida'] = $this->correspondencia_recebida_model->salvar($result, $args);

		for($i = 0; $i < count($arr); $i++)
		{
			$args['cd_correspondencia_recebida_item'] = $arr[$i];
		
			$this->correspondencia_recebida_model->correspondecia_items_recusados($result, $args);
		}
		
		echo intval($args['cd_correspondencia_recebida']);
	}
}

?>