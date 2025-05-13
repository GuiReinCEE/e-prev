<?php

class Plano_fiscal_indicador extends Controller
{
    var $fl_acesso = false;
	
	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('gestao/plano_fiscal_indicador_model');
		
        if ((gerencia_in(array('GC'))) and ($this->session->userdata('tipo') == "G")) #Gerente GC
        {
            $this->fl_acesso = true;
        }
        elseif ((gerencia_in(array('GC'))) and ($this->session->userdata('indic_01') == "S")) #SubGerente GC
        {
            $this->fl_acesso = true;
        }		
        elseif ($this->session->userdata('usuario') == "mvoigt") #Milena
        {
            $this->fl_acesso = true;
        }	
        elseif ($this->session->userdata('usuario') == "raquelr") #Raquel Cristiane Rodrigues Ramos
        {
            $this->fl_acesso = true;
        }			
        elseif ($this->session->userdata('usuario') == "jseidler") #Jean Carlos Oliveira Seidler <jseidler@eletroceee.com.br>
        {
            $this->fl_acesso = true;
        }		
        elseif ($this->session->userdata('usuario') == "cgoncalves") #Cristina
        {
            $this->fl_acesso = true;
        }		
        elseif ($this->session->userdata('usuario') == "jfetter") #Jorge
        {
            $this->fl_acesso = true;
        }
		elseif ($this->session->userdata('usuario') == "lrodriguez") #Luciano
        {
            $this->fl_acesso = true;
        }
		elseif ($this->session->userdata('usuario') == "coliveira") #Cristiano
        {
            $this->fl_acesso = true;
        }	
		elseif ($this->session->userdata('usuario') == "anunes") #Adriana Nobre Nunes
        {
            $this->fl_acesso = true;
        }		
		else
        {
            $this->fl_acesso = false;
        }			
    }
    
    public function index()
    {
        if ($this->fl_acesso)
        {
            $data = Array();

            $this->load->view('gestao/plano_fiscal_indicador/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function listar()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['nr_ano'] = $this->input->post("nr_ano", TRUE);
            $args['nr_mes'] = $this->input->post("nr_mes", TRUE);
			
			manter_filtros($args);

            $this->plano_fiscal_indicador_model->listar($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_indicador/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function relatorio()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $this->plano_fiscal_indicador_model->comboStatus($result, $args);
            $data['arr_status'] = $result->result_array();	

            $this->load->view('gestao/plano_fiscal_indicador/relatorio', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function listar_relatorio()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['nr_ano']               = $this->input->post("nr_ano", TRUE);
            $args['nr_mes']               = $this->input->post("nr_mes", TRUE);
            $args['nr_item']              = $this->input->post("nr_item", TRUE);
            $args['usuario']              = $this->input->post("usuario", TRUE);
            $args['usuario_gerencia']     = $this->input->post("usuario_gerencia", TRUE);
            $args['responsavel']          = $this->input->post("responsavel", TRUE);
            $args['responsavel_gerencia'] = $this->input->post("responsavel_gerencia", TRUE);
            $args['dt_encaminhamento_ini']         = $this->input->post("dt_encaminhamento_ini", TRUE);
            $args['dt_encaminhamento_fim']         = $this->input->post("dt_encaminhamento_fim", TRUE);
            $args['dt_envio_ini']         = $this->input->post("dt_envio_ini", TRUE);
            $args['dt_envio_fim']         = $this->input->post("dt_envio_fim", TRUE);
            $args['dt_limite_ini']        = $this->input->post("dt_limite_ini", TRUE);
            $args['dt_limite_fim']        = $this->input->post("dt_limite_fim", TRUE);
            $args['dt_resposta_ini']      = $this->input->post("dt_resposta_ini", TRUE);
            $args['dt_resposta_fim']      = $this->input->post("dt_resposta_fim", TRUE);
            $args['dt_assinatura_ini']    = $this->input->post("dt_assinatura_ini", TRUE);
            $args['dt_assinatura_fim']    = $this->input->post("dt_assinatura_fim", TRUE);
            $args['fl_assinado']          = $this->input->post("fl_assinado", TRUE);
            $args['fl_status']            = $this->input->post("fl_status", TRUE);
			
			manter_filtros($args);

            $this->plano_fiscal_indicador_model->listar_relatorio($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_indicador/relatorio_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function cadastro($cd_plano_fiscal_indicador = 0)
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_plano_fiscal_indicador'] = intval($cd_plano_fiscal_indicador);
            $args['cd_plano_fiscal_indicador'] = intval($cd_plano_fiscal_indicador);

            $this->plano_fiscal_indicador_model->total_enviados($result, $args);
            $data['total_enviados'] = $result->row_array();
            
            $this->plano_fiscal_indicador_model->unidade($result, $args);
            $data['arr_unidade'] = $result->result_array();
            
            $this->plano_fiscal_indicador_model->get_usuarios_de($result, $args);
            $data['arr_diretoria'] = $result->result_array();
			
            $this->plano_fiscal_indicador_model->comboStatus($result, $args);
            $data['arr_status'] = $result->result_array();		

            $this->plano_fiscal_indicador_model->area($result, $args);
            $data['arr_area'] = $result->result_array();			

            $data['periodicidade'] = $this->plano_fiscal_indicador_model->get_periodicidade();	

            if ($cd_plano_fiscal_indicador == 0)
            {
                $data['row'] = Array(
					'cd_plano_fiscal_indicador' => 0,
					'nr_ano'                    => date('Y'),
					'nr_mes'                    => date('m'),
					'cd_dir_administrativo'     => '',
					'cd_dir_financeiro'         => '',
					'cd_dir_seguridade'         => '',
					'cd_dir_seguridade'         => '',
					'cd_presidente'             => '',
					'dt_encerra'                => '',
					'cd_dir_administrativo_sub' => '',
                    'cd_dir_financeiro_sub'     => '',
                    'cd_dir_seguridade_sub'     => '',
					'cd_presidente_sub'         => '',
                    'ds_justificativa'
                ); 
            }
            else
            {
                $this->plano_fiscal_indicador_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('gestao/plano_fiscal_indicador/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_indicador'] = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['nr_ano']                    = $this->input->post("nr_ano", TRUE);
            $args['nr_mes']                    = $this->input->post("nr_mes", TRUE);
            $args['cd_dir_financeiro']         = $this->input->post("cd_dir_financeiro", TRUE);
            $args['cd_dir_administrativo']     = $this->input->post("cd_dir_administrativo", TRUE);
            $args['cd_dir_seguridade']         = $this->input->post("cd_dir_seguridade", TRUE);
            $args['cd_presidente']             = $this->input->post("cd_presidente", TRUE);
			$args['cd_dir_financeiro_sub']     = $this->input->post("cd_dir_financeiro_sub", TRUE);
            $args['cd_dir_administrativo_sub'] = $this->input->post("cd_dir_administrativo_sub", TRUE);
            $args['cd_dir_seguridade_sub']     = $this->input->post("cd_dir_seguridade_sub", TRUE);
            $args['cd_presidente_sub']         = $this->input->post("cd_presidente_sub", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');
            
            $retorno = $this->plano_fiscal_indicador_model->salvar($result, $args);

            redirect("gestao/plano_fiscal_indicador/cadastro/".$retorno, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function salvar_item()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_indicador']               = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['cd_plano_fiscal_indicador_item']          = $this->input->post("cd_plano_fiscal_indicador_item", TRUE);
            $args['cd_responsavel']                          = $this->input->post("usuario", TRUE);
            $args['cd_gerencia']                             = $this->input->post("usuario_gerencia", TRUE);
            $args['descricao']                               = $this->input->post("descricao", TRUE);
            $args['nr_item']                                 = $this->input->post("nr_item", TRUE);
            $args['cd_usuario']                              = $this->session->userdata('codigo');
            $args['peso']                                    = app_decimal_para_db($this->input->post("peso", TRUE));
            $args['meta']                                    = $this->input->post("meta", TRUE);
            $args['unidade']                                 = $this->input->post("unidade", TRUE);
            $args['cd_gerente']                              = $this->input->post("responsavel", TRUE);
            $args['cd_gerencia_gerente']                     = $this->input->post("responsavel_gerencia", TRUE);
            $args['resultado']                               = $this->input->post("resultado", TRUE);
			$args['retorno']                                 = $this->input->post("retorno", TRUE);
			$args['dt_limite']                               = $this->input->post("dt_limite", TRUE);
			$args['fl_status']                               = $this->input->post("fl_status", TRUE);
			$args['fl_copiar_resultado']                     = $this->input->post("fl_copiar_resultado", TRUE);
			$args['cd_plano_fiscal_indicador_area']          = $this->input->post("cd_plano_fiscal_indicador_area", TRUE);
            $args['fl_criterio']                             = $this->input->post("fl_criterio", TRUE);
            $args['cd_plano_fiscal_indicador_periodicidade'] = $this->input->post("cd_plano_fiscal_indicador_periodicidade", TRUE);

            $this->plano_fiscal_indicador_model->salvar_item($result, $args);

            redirect("gestao/plano_fiscal_indicador/cadastro/".$args['cd_plano_fiscal_indicador'], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function listar_itens()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_indicador']      = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['fl_respondido']                  = $this->input->post("fl_respondido", TRUE);
            $args['fl_assinado']                    = $this->input->post("fl_assinado", TRUE);
            $args['cd_plano_fiscal_indicador_area'] = $this->input->post("cd_plano_fiscal_indicador_area", TRUE);
			$args['fl_status_filtro']               = $this->input->post("fl_status_filtro", TRUE);

            $this->plano_fiscal_indicador_model->listar_itens($result, $args);

            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_indicador/item_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function carrega_item()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $arr = array();
            $result = null; 

            $args['cd_plano_fiscal_indicador_item'] = $this->input->post("cd_plano_fiscal_indicador_item", TRUE);

            $this->plano_fiscal_indicador_model->carrega_item($result, $args);
            $row = $result->row_array();
            
            $arr['cd_plano_fiscal_indicador_item']          = $row['cd_plano_fiscal_indicador_item'];
            $arr['nr_item']                                 = $row['nr_item'];
            $arr['descricao']                               = $row['descricao'];
            $arr['cd_gerencia']                             = $row['cd_gerencia'];
            $arr['cd_responsavel']                          = $row['cd_responsavel'];
            $arr['peso']                                    = number_format($row['peso'], 2, ',', '.');
            $arr['meta']                                    = $row['meta'];
            $arr['unidade']                                 = $row['unidade'];
            $arr['cd_gerente']                              = $row['cd_gerente'];
            $arr['cd_gerencia_gerente']                     = $row['cd_gerencia_gerente'];
            $arr['resultado']                               = $row['resultado'];
            $arr['dt_limite']                               = $row['dt_limite'];
            $arr['dt_resposta']                             = $row['dt_resposta'];
            $arr['dt_confirmacao']                          = $row['dt_confirmacao'];
			$arr['retorno']                                 = $row['retorno'];
			$arr['fl_status']                               = $row['fl_status'];
			$arr['cd_plano_fiscal_indicador_area']          = $row['cd_plano_fiscal_indicador_area'];
            $arr['fl_criterio']                             = $row['fl_criterio'];
            $arr['cd_plano_fiscal_indicador_periodicidade'] = $row['cd_plano_fiscal_indicador_periodicidade'];

            $arr = array_map("arrayToUTF8", $arr);
            echo json_encode($arr);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir_plano_item($cd_plano_fiscal_indicador, $cd_plano_fiscal_indicador_item)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_indicador']      = $cd_plano_fiscal_indicador;
            $args['cd_plano_fiscal_indicador_item'] = $cd_plano_fiscal_indicador_item;
            $args['cd_usuario']                     = $this->session->userdata('codigo');

            $this->plano_fiscal_indicador_model->excluir_plano_item($result, $args);

            redirect("gestao/plano_fiscal_indicador/cadastro/".$cd_plano_fiscal_indicador, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function prorrogacao()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_plano_fiscal_indicador'] = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['dt_limite']                 = $this->input->post("dt_prorrogaca", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->plano_fiscal_indicador_model->prorrogacao($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function excluir_plano($cd_plano_fiscal_indicador)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_indicador'] = $cd_plano_fiscal_indicador;
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->plano_fiscal_indicador_model->excluir_plano($result, $args);

            redirect("gestao/plano_fiscal_indicador", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function enviar($cd_plano_fiscal_indicador, $cd_plano_fiscal_indicador_item = 0)
    {
        if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_indicador']      = $cd_plano_fiscal_indicador;
            $args['cd_plano_fiscal_indicador_item'] = $cd_plano_fiscal_indicador_item;
            $args['cd_usuario']                     = $this->session->userdata('codigo');

            $this->plano_fiscal_indicador_model->enviar($result, $args);

            redirect("gestao/plano_fiscal_indicador/cadastro/".$cd_plano_fiscal_indicador, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
        
    function resposta($cd_plano_fiscal_indicador_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_indicador_item'] = $cd_plano_fiscal_indicador_item;
        $args['cd_usuario']                     = $this->session->userdata('codigo');
        
        $this->plano_fiscal_indicador_model->carrega_indicador_item_resposta($result, $args);
        $data['row'] = $result->row_array();
		
		$this->plano_fiscal_indicador_model->comboStatus($result, $args);
		$data['arr_status'] = $result->result_array();		
        
        if(
			($data['row']['cd_responsavel'] == $args['cd_usuario']) #RESPONDENTE
			OR ($data['row']['cd_gerente'] == $args['cd_usuario']) #RESPONSAVEL
			OR ($this->session->userdata('divisao') == "DE") #DIRETOR
			OR (($this->session->userdata('tipo') == "G") AND ($data['row']['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE
			OR (($this->session->userdata('indic_01') == "S") AND ($data['row']['cd_gerencia_gerente'] == $this->session->userdata('divisao')))#GERENTE SUBSTITUTO
		  )
        {
            $this->load->view('gestao/plano_fiscal_indicador/resposta', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_resposta()
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_indicador_item'] = $this->input->post("cd_plano_fiscal_indicador_item", TRUE);
        $args['fl_status']                      = $this->input->post("fl_status", TRUE);
        $args['resultado']                      = $this->input->post("resultado", TRUE);
        $args['cd_usuario']                     = $this->session->userdata('codigo');
        
        $this->plano_fiscal_indicador_model->salvar_resposta($result, $args);
        
        redirect("gestao/plano_fiscal_indicador/resposta/".$args['cd_plano_fiscal_indicador_item'], "refresh");
    }
    
    function confirmar()
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_indicador_item'] = $this->input->post("cd_plano_fiscal_indicador_item", TRUE);
        $args['fl_status']                      = $this->input->post("fl_status", TRUE);
        $args['resultado']                      = $this->input->post("resultado", TRUE);
        $args['cd_usuario']                     = $this->session->userdata('codigo');
		
		#$this->plano_fiscal_indicador_model->salvar_resposta($result, $args);
        
        $this->plano_fiscal_indicador_model->confirmar($result, $args);
        
        redirect("gestao/plano_fiscal_indicador/resposta/".$args['cd_plano_fiscal_indicador_item'], "refresh");
    }
	
	function reabrir($cd_plano_fiscal_indicador, $cd_plano_fiscal_indicador_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_indicador']      = $cd_plano_fiscal_indicador;
        $args['cd_plano_fiscal_indicador_item'] = $cd_plano_fiscal_indicador_item;
        $args['cd_usuario']                     = $this->session->userdata('codigo');
		
        $this->plano_fiscal_indicador_model->reabrir($result, $args);
        
        redirect("gestao/plano_fiscal_indicador/cadastro/".$cd_plano_fiscal_indicador, "refresh");
    }
    
    function encaminhar($cd_plano_fiscal_indicador_item)
    {
        $args = Array();
        $data = Array();
        $result = null; 
        
        $args['cd_plano_fiscal_indicador_item'] = $cd_plano_fiscal_indicador_item;
        $args['cd_usuario']                     = $this->session->userdata('codigo'); 
        
        $this->plano_fiscal_indicador_model->encaminhar($result, $args);
        
        redirect("gestao/plano_fiscal_indicador/resposta/".$args['cd_plano_fiscal_indicador_item'], "refresh");
    }
    
	function minhas()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
        $this->plano_fiscal_indicador_model->comboAnoMes($result, $args);
        $data['ar_ano_mes'] = $result->result_array();		
		
        $this->plano_fiscal_indicador_model->comboStatus($result, $args);
        $data['ar_status'] = $result->result_array();			
			
		$this->load->view('gestao/plano_fiscal_indicador/minhas.php', $data);
    }
	
	function minhas_listar()
    {
        $args = array();
        $data = array();
        $result = null;

        $args['cd_usuario']    = $this->session->userdata('codigo');
        $args['nr_ano_mes']    = $this->input->post("nr_ano_mes", TRUE);
        $args['fl_status']     = $this->input->post("fl_status", TRUE);
        $args['fl_respondido'] = $this->input->post("fl_respondido", TRUE);
		$args['fl_assinado']   = $this->input->post("fl_assinado", TRUE);
        $args['dt_ini_envio']  = $this->input->post("dt_ini_envio", TRUE);
        $args['dt_fim_envio']  = $this->input->post("dt_fim_envio", TRUE);
        $args['dt_ini_resp']   = $this->input->post("dt_ini_resp", TRUE);
        $args['dt_fim_resp']   = $this->input->post("dt_fim_resp", TRUE);

        manter_filtros($args);

        $this->plano_fiscal_indicador_model->carrega_minhas($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/plano_fiscal_indicador/minhas_result', $data);
    }
    
    public function encerrar()
    {
        if ($this->fl_acesso)
        {
            $result = null;

            $args = array(
                'cd_plano_fiscal_indicador' => $this->input->post('cd_plano_fiscal_indicador', TRUE),
                'ds_justificativa'          => $this->input->post('ds_justificativa', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );

            $this->plano_fiscal_indicador_model->encerrar($result, $args);

            redirect('gestao/plano_fiscal_indicador/cadastro/'.intval($args['cd_plano_fiscal_indicador']), 'refresh');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function diretoria($cd_plano_fiscal_indicador)
	{
		if ($this->fl_acesso)
        {
            $args['cd_plano_fiscal_indicador'] = $cd_plano_fiscal_indicador;
			
			$this->plano_fiscal_indicador_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->plano_fiscal_indicador_model->lista_assinatura_diretoria_parecer($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/plano_fiscal_indicador/diretoria', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar_limite_diretoria()
    {
        if ($this->fl_acesso)
        {
			$args['cd_plano_fiscal_indicador'] = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['dt_limite_diretoria']       = $this->input->post("dt_limite_diretoria", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->plano_fiscal_indicador_model->salvar_limite_diretoria($result, $args);

            redirect("gestao/plano_fiscal_indicador/diretoria/".intval($args['cd_plano_fiscal_indicador']), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function diretoria_assinar()
	{
		if(
			($this->session->userdata('divisao') == "DE") #DIRETORIA  
		     OR ($this->session->userdata('codigo') == 251) #lrodriguez 
			 OR ($this->session->userdata('codigo') == 170) #coliveira 
		  )
		{
			$args = Array();
			$data = Array();
			$result = null;

			$this->load->view('gestao/plano_fiscal_indicador/diretoria_assinar', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function listar_diretoria_assinar()
    {
        if(
			($this->session->userdata('divisao') == "DE") #DIRETORIA  
		     OR ($this->session->userdata('codigo') == 251) #lrodriguez 
			 OR ($this->session->userdata('codigo') == 170) #coliveira 
		  )
		{
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['nr_ano']      = $this->input->post("nr_ano", TRUE);
            $args['nr_mes']      = $this->input->post("nr_mes", TRUE);
			$args['fl_assinado'] = $this->input->post("fl_assinado", TRUE);
			$args['cd_usuario']  = $this->session->userdata('codigo'); 
			
			manter_filtros($args);

            $this->plano_fiscal_indicador_model->listar_diretoria_assinar($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/plano_fiscal_indicador/diretoria_assinar_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function assinar($cd_plano_fiscal_indicador, $cd_diretoria)
    {
        if(
			($this->session->userdata('divisao') == "DE") #DIRETORIA  
		     OR ($this->session->userdata('codigo') == 251) #lrodriguez 
			 OR ($this->session->userdata('codigo') == 170) #coliveira 
		  )
		{
            $args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_plano_fiscal_indicador'] = $cd_plano_fiscal_indicador;
            $args['cd_diretoria']              = $cd_diretoria;
			$args['cd_usuario']                = $this->session->userdata('codigo');
			
			$where_cd = '';
			
			switch ($cd_diretoria) 
			{
				case 'FIN':
					$where_cd = 'cd_dir_financeiro';
					break;
				case 'ADM':
					$where_cd = 'cd_dir_administrativo';
					break;
				case 'INFR':
					$where_cd = 'cd_dir_administrativo';
					break;					
				case 'SEG':
					$where_cd = 'cd_dir_seguridade';
					break;
				case 'PREV':
					$where_cd = 'cd_dir_seguridade';
					break;					
				case 'PRE':
					$where_cd = 'cd_presidente';
					break;
			}
			
			$args['where_cd'] = $where_cd;
			
			$this->plano_fiscal_indicador_model->carrega_assinar($result, $args);
			$data['row'] = $result->row_array();

			if(count($data['row']) > 0)
			{
				$this->load->view('gestao/plano_fiscal_indicador/assinar', $data);
			}
			else
			{
				exibir_mensagem("ACESSO NÃO PERMITIDO");
			}
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function salvar_diretoria_assinar()
	{
		if(
			($this->session->userdata('divisao') == "DE") #DIRETORIA  
		     OR ($this->session->userdata('codigo') == 251) #lrodriguez 
			 OR ($this->session->userdata('codigo') == 170) #coliveira 
		  )
		{
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_plano_fiscal_indicador'] = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['cd_diretoria']              = $this->input->post("cd_diretoria", TRUE);
			$args['cd_usuario']                = $this->session->userdata('codigo');
			
			$this->plano_fiscal_indicador_model->salvar_diretoria_assinar($result, $args);
			
			redirect("gestao/plano_fiscal_indicador/diretoria_assinar", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function imprimir()
    {
        if ($this->fl_acesso)
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_indicador']      = $this->input->post("cd_plano_fiscal_indicador", TRUE);
            $args['fl_respondido']                  = "";
            $args['fl_assinado']                    = "";
            $args['fl_status_filtro']               = "";
            $args['cd_plano_fiscal_indicador_area'] = "";			
			
			$this->plano_fiscal_indicador_model->carrega($result, $args);
            $row = $result->row_array();

            $this->plano_fiscal_indicador_model->listar_itens($result, $args);
            $collection = $result->result_array();
			
			#### EXCEL ####
			$this->load->plugin('phpexcel');
			$nr_col_ini  = 0;
			$nr_col_fim  = 0;
			$nr_row_ini  = 7;	
			
			#### Create new PHPExcel object ####
			$objPHPExcel = new PHPExcel();

			#### CRIA PLANILHA ####
			$objPHPExcel->setActiveSheetIndex(0);	
			$objPHPExcel->getActiveSheet()->setTitle('Indicador');
			
			#### LOGO ####
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Terms and conditions');
			$objDrawing->setDescription('Terms and conditions');
			list($width, $height) = getimagesize('./img/logofundacao_carta.jpg'); 
			$objDrawing->setPath('./img/logofundacao_carta.jpg');
			$objDrawing->setCoordinates("A1");
			$objDrawing->setHeight(38);
			$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());		
			
			#### TITULO ###
			

			$objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', utf8_encode("PLANO DE FISCALIZAÇÃO - CONSELHO FISCAL - INDICADORES DE GESTÃO DO PGA"));
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
			$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->mergeCells('A3:I3');
			$objPHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode('(Em atendimento ao parágrafo único do art. 12 e ao art. 38 do Regulamento do PGA)'));
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);	
			
			
			$objPHPExcel->getActiveSheet()->mergeCells('A4:H4');
			$objPHPExcel->getActiveSheet()->setCellValue('A4', utf8_encode('Referente: '.$row['nr_mes'].'/'.$row['nr_ano']));
			$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->mergeCells('A5:E5');
			$objPHPExcel->getActiveSheet()->setCellValue('A5', utf8_encode(date('d/m/Y H:i:s')));
			$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(false);	
			
			#### CABEÇALHO ####
			$nr_row = $nr_row_ini;
			$nr_col = $nr_col_ini;

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode("Item"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode("Indicador/Método Cálculo"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode("Critério"));
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode("Periodicidade"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode("Meta"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode("Status"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode("Resultado"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, utf8_encode("Gerência"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 8, $nr_row, utf8_encode("Responsável"));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 9, $nr_row, utf8_encode("Assinatura"));
			
			#### TAMANHO COLUNA ASSINATURA ####
			$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(30);
			$nr_col_fim+=8;
			
			#### TABELA DO PARECER ####
			$nr_row = $nr_row_ini + 1;
            foreach($collection as $item)
            {
				$status = $item['ds_status'];
                $args['cd_usuario'] = intval($item['cd_usuario_confirmacao']);
                
                $this->plano_fiscal_indicador_model->get_assinatura($result, $args);
				$assinatura = (count($result->row_array()) > 0 ? $result->row_array() : array('assinatura'=>''));
				
				$nr_col = $nr_col_ini;
				
				$objPHPExcel->getActiveSheet()->getRowDimension($nr_row)->setRowHeight(70);
				
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode($item['nr_item']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode($item['descricao']));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode($item['criterio']));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode($item['ds_plano_fiscal_indicador_periodicidade']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, (trim($item['meta']) != '' ? $item['meta'] : '').(trim($item['unidade']) != '' ? ' '.utf8_encode($item['unidade']) : ''));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode($status));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode($item['resultado']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, utf8_encode($item['ds_plano_fiscal_indicador_area']));
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 8, $nr_row, utf8_encode((trim($item['dt_confirmacao']) != '' ? (trim($assinatura['assinatura']) != '' ? "" : '') .utf8_encode($item['usuario_confirmacao']) : '')));
				
				if((trim($assinatura['assinatura']) != '') AND (trim($item['dt_confirmacao']) != ''))
                {
					#### ASSINATURA ####
					$objDrawing = new PHPExcel_Worksheet_Drawing();
					$objDrawing->setName('Terms and conditions');
					$objDrawing->setDescription('Terms and conditions');
					list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']); 
					$objDrawing->setPath('./img/assinatura/'.$assinatura['assinatura']);
					$col_assina = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col + 9, $nr_row)->getCoordinate();
					$objDrawing->setCoordinates($col_assina);
					$objDrawing->setHeight($height/5);
					$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());					
				}

				$nr_row++;	
			}
			
			#### FORMATA CABEÇALHO DA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row_ini)->getCoordinate();
			$sharedStyle = new PHPExcel_Style();
			$sharedStyle->applyFromArray(
				array(
							'font' => array(
								'bold' => true,
								'size' => 14
								
							),
							'alignment' => array(
								'wrap' => true, 
								'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
							),
							'borders' => array(
								'top'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'bottom'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'left'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								),
								'right'     => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)								
							),							
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
								'rotation' => 90,
								'startcolor' => array(
									'argb' => 'FFA0A0A0'
								),
								'endcolor' => array(
									'argb' => 'FFFFFFFF'
								)
							)
					));			
			
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);		

			#### FORMATA TABELA ####
			$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini + 1)->getCoordinate();
			$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row)->getCoordinate();			
	
			$sharedStyle = new PHPExcel_Style();
			$sharedStyle->applyFromArray(
				array('borders' => array(
											'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
											'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
											'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
										),
						'alignment' => array(
										'wrap' => true, 
										'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
									),
                        'font' => array(
							'size' => 14
						)									
					 ));			
			
			$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);
			
			$nr_row = $nr_row+3;
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$nr_row, utf8_encode("Ciente,  "));
			$objPHPExcel->getActiveSheet()->getStyle('A'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$nr_row)->getFont()->setBold(true);	
			
			$nr_row = $nr_row+3;
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$nr_row, utf8_encode($row['usuario_presidente']));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$nr_row)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$nr_row)->getFont()->setBold(true);	
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($nr_row+1), utf8_encode($row['obs_presidente']));
			$objPHPExcel->getActiveSheet()->getStyle('B'.($nr_row+1))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($nr_row+1))->getFont()->setBold(true);	
			
            if(trim($row['usuario_dir_financeiro']) != '')
            {
    			$objPHPExcel->getActiveSheet()->setCellValue('D'.$nr_row, utf8_encode($row['usuario_dir_financeiro']));
    			$objPHPExcel->getActiveSheet()->getStyle('D'.$nr_row)->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('D'.$nr_row)->getFont()->setBold(true);	
    			
    			$objPHPExcel->getActiveSheet()->setCellValue('D'.($nr_row+1), utf8_encode($row['obs_financeiro']));
    			$objPHPExcel->getActiveSheet()->getStyle('D'.($nr_row+1))->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('D'.($nr_row+1))->getFont()->setBold(true);	
            }
			
            if(trim($row['usuario_dir_seguridade']) != '')
            {
    			$objPHPExcel->getActiveSheet()->setCellValue('G'.$nr_row, utf8_encode($row['usuario_dir_seguridade']));
    			$objPHPExcel->getActiveSheet()->getStyle('G'.$nr_row)->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('G'.$nr_row)->getFont()->setBold(true);	
    			
    			$objPHPExcel->getActiveSheet()->setCellValue('G'.($nr_row+1), utf8_encode($row['obs_seguridade']));
    			$objPHPExcel->getActiveSheet()->getStyle('G'.($nr_row+1))->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('G'.($nr_row+1))->getFont()->setBold(true);
            }	
			
            if(trim($row['usuario_dir_administrativo']) != '')
            {
    			$objPHPExcel->getActiveSheet()->setCellValue('J'.$nr_row, utf8_encode($row['usuario_dir_administrativo']));
    			$objPHPExcel->getActiveSheet()->getStyle('J'.$nr_row)->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('J'.$nr_row)->getFont()->setBold(true);	
    			
    			$objPHPExcel->getActiveSheet()->setCellValue('J'.($nr_row+1), utf8_encode($row['obs_administrativo']));
    			$objPHPExcel->getActiveSheet()->getStyle('J'.($nr_row+1))->getFont()->setSize(12);
    			$objPHPExcel->getActiveSheet()->getStyle('J'.($nr_row+1))->getFont()->setBold(true);
            }

            if(trim($row['ds_justificativa']) != '')
            {
                $objPHPExcel->getActiveSheet()->mergeCells('A'.($nr_row+5).':D'.($nr_row+5));
                $objPHPExcel->getActiveSheet()->setCellValue('A'.($nr_row+5), 'Justificativa do Encerramento :');

                $objPHPExcel->getActiveSheet()->mergeCells('A'.($nr_row+6).':L'.($nr_row+6));
                $objPHPExcel->getActiveSheet()->setCellValue('A'.($nr_row+6), utf8_encode($row['ds_justificativa']));
            } 
	
			#### GERA EXCEL ####
			$ds_xls = random_string().'.xlsx';
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="'.$ds_xls.'"');
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');	
            exit;
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
    function imprimirPDF($cd_plano_fiscal_indicador = 0)
    {
        if (($this->fl_acesso) OR ($this->session->userdata('divisao') == "DE"))
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_plano_fiscal_indicador']      = (intval($this->input->post("cd_plano_fiscal_indicador", TRUE)) > 0 ? intval($this->input->post("cd_plano_fiscal_indicador", TRUE)) : intval($cd_plano_fiscal_indicador));
            $args['fl_respondido']                  = "";
            $args['fl_assinado']                    = "";
			$args['fl_status_filtro']               = "";
            $args['cd_plano_fiscal_indicador_area'] = "";	

            $this->load->plugin('fpdf');
            
            $this->plano_fiscal_indicador_model->carrega($result, $args);
            $row = $result->row_array();

            $this->plano_fiscal_indicador_model->listar_itens($result, $args);
            $collection = $result->result_array();
 
            $ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');			
            $ob_pdf->SetNrPagDe(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "PLANO DE FISCALIZAÇÃO - CONSELHO FISCAL - INDICADORES DE GESTÃO DO PGA";
            $ob_pdf->header_subtitulo = true;
			$ob_pdf->header_subtitulo_texto = 'Referente: '.$row['nr_mes'].'/'.$row['nr_ano']."\n"."(Em atendimento ao parágrafo do art. 12 e ao art. 38 do Regulamento do PGA)";	
            $ob_pdf->AddPage();	

            foreach($collection as $item)
            {
                $status = $item['ds_status'];
                $args['cd_usuario'] = intval($item['cd_usuario_confirmacao']);
                
                $this->plano_fiscal_indicador_model->get_assinatura($result, $args);
                $assinatura = (count($result->row_array()) > 0 ? $result->row_array() : array('assinatura'=>''));
                
                if(trim($assinatura['assinatura']) != '')
                {
                    list($width, $height) = getimagesize('./img/assinatura/'.$assinatura['assinatura']);   
                }
                
                $ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Item:", '0', 'L');
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->MultiCell(190, 5, $item['nr_item'].' - '.$item['descricao'], '0', 'J');					
				
				$ob_pdf->SetY($ob_pdf->GetY() + 3);

                if(trim($item['criterio']) != "")
                {
                    $ob_pdf->SetFont('segoeuib','',11);
                    $ob_pdf->MultiCell(190, 5, "Critério:", '0', 'L');
                    $ob_pdf->SetFont('segoeuil','',10);
                    $ob_pdf->MultiCell(190, 5, $item['criterio'], '0', 'L');    

                    $ob_pdf->SetY($ob_pdf->GetY() + 3);
                }

                $ob_pdf->SetFont('segoeuib','',11);
                $ob_pdf->MultiCell(190, 5, "Periodicidade:", '0', 'L');
                $ob_pdf->SetFont('segoeuil','',10);
                $ob_pdf->MultiCell(190, 5, $item['ds_plano_fiscal_indicador_periodicidade'], '0', 'L');    

                $ob_pdf->SetY($ob_pdf->GetY() + 3);
                
                $ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Meta:", '0', 'L');
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->MultiCell(190, 5, (trim($item['meta']) != '' ? $item['meta'] : ''), '0', 'L');		
                
                $ob_pdf->SetY($ob_pdf->GetY() + 3);

				$ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Status:", '0', 'L');
				$ob_pdf->SetFont('segoeuib','',10);
				if($status == "NÃO ATENDE")
				{
					$ob_pdf->SetTextColor(220,50,50);
				}
				elseif($status == "ATENDE")
				{
					$ob_pdf->SetTextColor(0,127,14);
				}
				elseif($status == "JUSTIFICATIVA")
				{
					$ob_pdf->SetTextColor(255,140,0);
				}				
				$ob_pdf->MultiCell(190, 5, $status, '0', 'L');
				$ob_pdf->SetTextColor(0,0,0);	

                $ob_pdf->SetY($ob_pdf->GetY() + 3);
				
				$ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Resultado:", '0', 'L');
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->MultiCell(190, 5, trim($item['resultado']), '0', 'J');				
				
                $ob_pdf->SetY($ob_pdf->GetY() + 4);
				
				if($ob_pdf->GetY() >= 258) 
				{
					#### FORCA A QUEBRA DA PAGINA ####
					$ob_pdf->AddPage();
				}
				
				$ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Gerência:", '0', 'L');
				$ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->MultiCell(190, 5, $item['ds_plano_fiscal_indicador_area'], '0', 'J');					
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				
				$ob_pdf->SetFont('segoeuib','',11);
				$ob_pdf->MultiCell(190, 5, "Responsável:", '0', 'L');
                
                if((trim($assinatura['assinatura']) != '') AND (trim($item['dt_confirmacao']) != ''))
                {
                    $ob_pdf->Image('./img/assinatura/'.$assinatura['assinatura'], 80, $ob_pdf->GetY() - 22, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
                }
                
                $ob_pdf->SetFont('segoeuil','',10);
				$ob_pdf->MultiCell(190, 5, (trim($item['dt_confirmacao']) != '' ? $item['usuario_confirmacao'] : ''), '0', 'J');				
				
                #$ob_pdf->AddPage();	
				
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell(190, 5, "-------------------------------------------------------------------------------------------------------------------------------------", '0', 'J');
				$ob_pdf->SetY($ob_pdf->GetY() + 5);					
               
				if($ob_pdf->GetY() >= 260) 
				{
					#### FORCA A QUEBRA DA PAGINA ####
					$ob_pdf->AddPage();
				}				   
            }
            
            if(($ob_pdf->GetY()+ 8) >= 192)
            {
                $ob_pdf->AddPage();	
            }
            
            #### ASSINATURA DA DIRETORIA ####
            $ob_pdf->SetY($ob_pdf->GetY() + 8);
			$ob_pdf->SetFont('segoeuil','',12);
            $ob_pdf->Text(10, $ob_pdf->GetY(), "Ciente,");
            $ob_pdf->SetY($ob_pdf->GetY() + 30); 
			
			#### PRESINDENTE ####
			$row['usuario_presidente'] = (((intval($row['cd_presidente']) != intval($row['cd_assinatura_presidente'])) and (intval($row['cd_assinatura_presidente']) > 0)) ? "pp ".$row['usuario_presidente'] : $row['usuario_presidente']);
            $nr_pe = $ob_pdf->GetStringWidth($row['usuario_presidente']);
            $nr_c_pe = $ob_pdf->GetStringWidth($row['obs_presidente']);
            $coluna_pe    = abs(($nr_pe-$nr_c_pe)/2);
            $coluna_nm_pe = abs(($nr_pe-$nr_c_pe)/2);
            
            if($nr_pe > $nr_c_pe)
            {
                $coluna_nm_pe = 0;
                $nr_x = $nr_pe;
            }
            else
            {
                $coluna_pe = 0;
                $nr_x = $nr_c_pe;
            }
            
            $ob_pdf->SetX(20);
            
			if((trim($row['assinatura_presidente']) != "") and (trim($row["dt_assinatura_presidente"]) != ""))
			{
				$ob_pdf->Image('./img/assinatura/'.$row['assinatura_presidente'], 4.5, $ob_pdf->GetY() - 26, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
			}
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_pe, $ob_pdf->GetY(), $row['usuario_presidente']);
            $ob_pdf->Text($ob_pdf->GetX() + $coluna_pe , $ob_pdf->GetY()+4, $row['obs_presidente']);
			
			#### DIRETOR FINANCEIRO ####
            if(trim($row['usuario_dir_financeiro']) != '')
            {
    			$row['usuario_dir_financeiro'] = (((intval($row['cd_dir_financeiro']) != intval($row['cd_assinatura_dir_financeiro'])) and (intval($row['cd_assinatura_dir_financeiro']) > 0)) ? "pp ".$row['usuario_dir_financeiro'] : $row['usuario_dir_financeiro']);
                $nr_df = $ob_pdf->GetStringWidth($row['usuario_dir_financeiro']);
                $nr_c_df = $ob_pdf->GetStringWidth($row['obs_financeiro']);
                
                $coluna_df    = abs(($nr_df-$nr_c_df)/2);
                $coluna_nm_df = abs(($nr_df-$nr_c_df)/2);
                
                $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
                
                if($nr_df > $nr_c_df)
                {
                    $coluna_nm_df = 0;
                    $nr_x = $nr_df;
                }
                else
                {
                    $coluna_df = 0;
                    $nr_x = $nr_c_df;
                }
                
    			if((trim($row['assinatura_dir_financeiro']) != "") and (trim($row["dt_assinatura_dir_financeiro"]) != ""))
    			{
    				$ob_pdf->Image('./img/assinatura/'.$row['assinatura_dir_financeiro'], $ob_pdf->GetX()+10, $ob_pdf->GetY() - 26, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
    			}				
                $ob_pdf->Text(110, $ob_pdf->GetY(), $row['usuario_dir_financeiro']);
                $ob_pdf->Text(110 + $coluna_df , $ob_pdf->GetY()+4, $row['obs_financeiro']);
            }
            
			#### DIRETOR SEGURIDADE ####
            if(trim($row['usuario_dir_seguridade']) != '')
            {
    			$row['usuario_dir_seguridade'] = (((intval($row['cd_dir_seguridade']) != intval($row['cd_assinatura_dir_seguridade'])) and (intval($row['cd_assinatura_dir_seguridade']) > 0)) ? "pp ".$row['usuario_dir_seguridade'] : $row['usuario_dir_seguridade']);
                $nr_ds = $ob_pdf->GetStringWidth($row['usuario_dir_seguridade']);
                $nr_c_ds = $ob_pdf->GetStringWidth($row['obs_seguridade']);
                
                $coluna_ds    = abs(($nr_ds-$nr_c_ds)/2);
                $coluna_nm_ds = abs(($nr_ds-$nr_c_ds)/2);
                
                $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
                
                if($nr_ds > $nr_c_ds)
                {
                    $coluna_nm_ds = 0;
                    $nr_x = $nr_ds;
                }
                else
                {
                    $coluna_ds = 0;
                    $nr_x = $nr_c_ds;
                }
    			
    			$ob_pdf->SetXY(20, $ob_pdf->GetY()+40);
    			
    			if((trim($row['assinatura_dir_seguridade']) != "") and (trim($row["dt_assinatura_dir_seguridade"]) != ""))
    			{
    				$ob_pdf->Image('./img/assinatura/'.$row['assinatura_dir_seguridade'], 4.5, $ob_pdf->GetY() - 26, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
    			}			
                $ob_pdf->Text($ob_pdf->GetX() + $coluna_nm_ds, $ob_pdf->GetY(), $row['usuario_dir_seguridade']);
                $ob_pdf->Text($ob_pdf->GetX() + $coluna_ds, $ob_pdf->GetY()+4, $row['obs_seguridade']);
            }
			
			#### DIRETOR ADMINISTRATIVO ####
            if(trim($row['usuario_dir_administrativo']) != '')
            {
    			$row['usuario_dir_administrativo'] = (((intval($row['cd_dir_administrativo']) != intval($row['cd_assinatura_dir_administrativo'])) and (intval($row['cd_assinatura_dir_administrativo']) > 0)) ? "pp ".$row['usuario_dir_administrativo'] : $row['usuario_dir_administrativo']);
                $nr_da = $ob_pdf->GetStringWidth($row['usuario_dir_administrativo']);
                $nr_c_da = $ob_pdf->GetStringWidth($row['obs_administrativo']);
                
                $coluna_da = abs(($nr_da-$nr_c_da)/2);
                $coluna_nm_da = abs(($nr_da-$nr_c_da)/2);
                
                $ob_pdf->SetX($ob_pdf->GetX()+$nr_x+15);
                
                if($nr_da > $nr_c_da)
                {
                    $coluna_nm_da = 0;
                    $nr_x = $nr_da;
                }
                else
                {
                    $coluna_da = 0;
                    $nr_x = $nr_c_da;
                }
                
    			if((trim($row['assinatura_dir_administrativo']) != "") and (trim($row["dt_assinatura_dir_administrativo"]) != ""))
    			{
    				$ob_pdf->Image('./img/assinatura/'.$row['assinatura_dir_administrativo'], $ob_pdf->GetX()+10, $ob_pdf->GetY() - 26, $ob_pdf->ConvertSize($width/3.5), $ob_pdf->ConvertSize($height/3.5),'','',false);
    			}			
                $ob_pdf->Text(110, $ob_pdf->GetY(), $row['usuario_dir_administrativo']);
                $ob_pdf->Text(110 + $coluna_da, $ob_pdf->GetY()+4, $row['obs_administrativo']);
            }

            if(trim($row['ds_justificativa']) != '')
            {
                $ob_pdf->SetY($ob_pdf->GetY() + 25);
                $ob_pdf->SetFont('segoeuil','',12);
                $ob_pdf->MultiCell(190, 5, 'Justificativa do Encerramento :', '0', 'L');
                $ob_pdf->MultiCell(190, 5, $row['ds_justificativa'], '0', 'J');      
            } 

            $ob_pdf->Output();
            exit;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>