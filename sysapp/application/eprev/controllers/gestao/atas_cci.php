<?php

class atas_cci extends Controller
{
	var $fl_libera = false;
	
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('gestao/atas_cci_model');
		
		$this->fl_libera = false;
		
        if ((gerencia_in(array('GC'))) and ($this->session->userdata('tipo') == "G")) #Gerente GC
        {
            $this->fl_acesso = true;
        }
        elseif ((gerencia_in(array('GC'))) and ($this->session->userdata('indic_01') == "S")) #SubGerente GC
        {
            $this->fl_acesso = true;
        }	
		elseif ($this->session->userdata('codigo') == 132) #Jorge Alexandre Fetter
		{
			$this->fl_libera = true;
		}
		elseif ($this->session->userdata('codigo') == 118) #Cristina Gomes Goncalves
		{
			$this->fl_libera = true;
		}		
		elseif ($this->session->userdata('codigo') == 26) #Adriana Nobre Nunes
		{
			$this->fl_libera = true;
		}
		elseif ($this->session->userdata('codigo') == 319) #Milena Voigt da Silva
		{
			$this->fl_libera = true;
		}
		elseif ($this->session->userdata('codigo') == 251) #Luciano Rodriguez
		{
			$this->fl_libera = true;
		}
    }

    private function permissao()
    {
    	if ((gerencia_in(array('GC'))) and ($this->session->userdata('tipo') == "G")) #Gerente GC
        {
            return true;
        }
        elseif ((gerencia_in(array('GC'))) and ($this->session->userdata('indic_01') == "S")) #SubGerente GC
        {
            return true;
        }	
		elseif ($this->session->userdata('codigo') == 132) #"Jorge Alexandre Fetter"
		{
			return true;
		}
		elseif ($this->session->userdata('codigo') == 118) #"Cristina Gomes Goncalves"
		{
			return true;
		}		
		elseif ($this->session->userdata('codigo') == 26) #Adriana Nobre Nunes
		{
			return true;
		}
		elseif ($this->session->userdata('codigo') == 441) #Clara Julia Brandolff dos Santos
		{
			return true;
		}
		elseif ($this->session->userdata('codigo') == 251) #Luciano Rodriguez
		{
			return true;
		}
		elseif ($this->session->userdata('codigo') == 319) #Milena Voigt da Silva
		{
			return true;
		}
		else
		{
			return false;
		}	
    }
	
	public function index()
    {
		if (gerencia_in(array('GC', 'GIN', 'DE', 'SG')))
        {
			$this->load->view('gestao/atas_cci/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	public function listar()
    {
		if (gerencia_in(array('GC', 'GIN', 'DE', 'SG')))
        {
			$args = array();
			$data = array();
			$result = null;
		
			$args['nr_ano']                 	   = $this->input->post("nr_ano", TRUE); 
			$args['nr_reuniao']              	   = $this->input->post("nr_reuniao", TRUE); 
			$args['fl_ata_cci']              	   = $this->input->post("fl_ata_cci", TRUE); 
			$args['fl_sumula_cci']           	   = $this->input->post("fl_sumula_cci", TRUE); 
			$args['fl_anexo_cci']            	   = $this->input->post("fl_anexo_cci", TRUE); 
			$args['fl_homologado_diretoria'] 	   = $this->input->post("fl_homologado_diretoria", TRUE); 
			$args['fl_homologado_conselho_fiscal'] = $this->input->post("fl_homologado_conselho_fiscal", TRUE); 
			$args['fl_publicado_alchemy']    	   = $this->input->post("fl_publicado_alchemy", TRUE); 
			$args['fl_publicada_eprev']            = $this->input->post("fl_publicada_eprev", TRUE); 
			$args['fl_etapa']               	   = $this->input->post("fl_etapa", TRUE); 	
				
			manter_filtros($args);

			$this->atas_cci_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/atas_cci/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function cadastro($cd_atas_cci = 0)
    {
		$args = array();
		$data = array();
		$result = null;

		$args['cd_atas_cci'] = intval($cd_atas_cci);       
		
		$this->atas_cci_model->usuarios_investimento($result, $args);
		$data['ar_responsavel_gin'] = $result->result_array();

		$data['assinatura'] = array();

		if ((($this->permissao()) AND (gerencia_in(array('GC')))) OR (gerencia_in(array('GIN', 'SG'))) )
        {
            if ($args['cd_atas_cci'] == 0)
            {			
                 $data['row'] = Array(
                   'cd_atas_cci'                   => 0,
                   'nr_reuniao'                    => '',
                   'dt_reuniao'                    => '',
                   'dt_ata_cci'                    => '',
                   'dt_sumula_cci'                 => '',
                   'dt_anexo_cci'                  => '',
                   'fl_ata_cci'                    => '',
                   'fl_sumula_cci'                 => '',
                   'fl_anexo_cci'                  => '',
				   'nr_ata_diretoria'              => '',
				   'nr_ata_conselho_fiscal'        => '',
				   'fl_homologado_conselho_fiscal' => '',
				   'fl_publicado_alchemy'          => '',
				   'fl_publicado_eprev'            => '',
				   'dt_homologado_diretoria'       => '',
				   'dt_homologado_conselho_fiscal' => '',
				   'cd_responsavel_investimento'   => ''
                 );
            }
            else
            {
                $this->atas_cci_model->carrega($result, $args);
                $data['row'] = $result->row_array();
				
				$data['assinatura'] = $this->atas_cci_model->get_assinatura($cd_atas_cci);
            }

            $this->load->view('gestao/atas_cci/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar()
    {
		$args = array();
		$data = array();
		$result = null;
		
		$args['cd_atas_cci'] = $this->input->post("cd_atas_cci", TRUE);        
		
		if ((($this->permissao()) AND (gerencia_in(array('GC')))) OR (gerencia_in(array('GIN', 'SG'))))
        {
			$args['nr_reuniao']                    = $this->input->post("nr_reuniao", TRUE);
			$args['dt_reuniao']                    = $this->input->post("dt_reuniao", TRUE);
			$args['fl_ata_cci']                    = $this->input->post("fl_ata_cci", TRUE);
			$args['fl_sumula_cci']                 = $this->input->post("fl_sumula_cci", TRUE);
			$args['fl_anexo_cci']                  = $this->input->post("fl_anexo_cci", TRUE);
			$args['fl_homologado_conselho_fiscal'] = $this->input->post("fl_homologado_conselho_fiscal", TRUE);
			$args['nr_ata_diretoria']              = $this->input->post("nr_ata_diretoria", TRUE);
			$args['nr_ata_conselho_fiscal']        = $this->input->post("nr_ata_conselho_fiscal", TRUE);
			$args['fl_publicado_alchemy']          = $this->input->post("fl_publicado_alchemy", TRUE);
			$args['fl_publicado_eprev']            = $this->input->post("fl_publicado_eprev", TRUE);
			$args['dt_ata_cci']                    = $this->input->post("dt_ata_cci", TRUE);
			$args['dt_sumula_cci']                 = $this->input->post("dt_sumula_cci", TRUE);
			$args['dt_anexo_cci']                  = $this->input->post("dt_anexo_cci", TRUE);
			$args['dt_homologado_diretoria']       = $this->input->post("dt_homologado_diretoria", TRUE);
			$args['dt_homologado_conselho_fiscal'] = $this->input->post("dt_homologado_conselho_fiscal", TRUE);
			$args['cd_responsavel_investimento']   = $this->input->post("cd_responsavel_investimento", TRUE);
            $args['cd_usuario']                    = $this->session->userdata('codigo');

            $cd_atas_cci = $this->atas_cci_model->salvar($result, $args);
			redirect("gestao/atas_cci/cadastro/".$cd_atas_cci, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function excluir($cd_atas_cci)
	{
		if ($this->permissao())
        {
			$args = array();
			$data = array();
			$result = null;
			
			$args['cd_atas_cci'] = $cd_atas_cci;
			$args['cd_usuario']  = $this->session->userdata('codigo');
		
			$this->atas_cci_model->excluir($result, $args);
			
			redirect("gestao/atas_cci/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function acompanhamento($cd_atas_cci)
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$data['cd_atas_cci'] = $cd_atas_cci;
			
			$this->load->view('gestao/atas_cci/acompanhamento', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function carrega_acompanhamento()
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = array();
			$data = array();
			$result = null;
			
			$args['cd_atas_cci_acompanhamento'] = $this->input->post("cd_atas_cci_acompanhamento", TRUE);
			
			$this->atas_cci_model->carrega_acompanhamento($result, $args);
			$row = $result->row_array();

			echo json_encode(array('descricao' => utf8_encode($row['descricao'])));
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function listar_acompanhamento()
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_atas_cci'] = $this->input->post("cd_atas_cci", TRUE);
			$args['cd_gerencia'] = $this->session->userdata('divisao');
			
            $this->atas_cci_model->lista_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/atas_cci/acompanhamento_result', $data);
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function salvar_acompanhamento()
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_atas_cci']                = $this->input->post("cd_atas_cci", TRUE);
			$args['cd_atas_cci_acompanhamento'] = $this->input->post("cd_atas_cci_acompanhamento", TRUE);
			$args['descricao']                  = $this->input->post("descricao", TRUE);
			$args['cd_usuario']                 = $this->session->userdata('codigo');
			$args['cd_gerencia']                = $this->session->userdata('divisao');
			
			$this->atas_cci_model->salvar_acompanhamento($result, $args);
			
			redirect("gestao/atas_cci/acompanhamento/" . $args['cd_atas_cci'], "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_acompanhamento($cd_atas_cci, $cd_atas_cci_acompanhamento)
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_atas_cci']                = $cd_atas_cci;
			$args['cd_atas_cci_acompanhamento'] = $cd_atas_cci_acompanhamento;
			$args['cd_usuario']                 = $this->session->userdata('codigo');
			
            $this->atas_cci_model->excluir_acompanhamento($result, $args);
			
            redirect("gestao/atas_cci/acompanhamento/" . $args['cd_atas_cci'], "refresh");
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function lista_etapas_investimento()
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = Array();
            $data = Array();
            $result = null;
			
			$args['cd_atas_cci'] = $this->input->post("cd_atas_cci", TRUE);
			
            $this->atas_cci_model->lista_etapas_investimento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/atas_cci/cadastro_etapas_investimento_result', $data);
			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function checked_etapa()
	{
		if (($this->permissao()) OR (gerencia_in(array('GIN'))))
        {
			$args = Array();
            $data = Array();
            $result = null;

			$args['etapas']      = $this->input->post("etapas", TRUE);
			$args['cd_atas_cci'] = $this->input->post("cd_atas_cci", TRUE);
			$args['cd_usuario']  = $this->session->userdata('codigo');
	
			$this->atas_cci_model->checked_etapa($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
}
?>