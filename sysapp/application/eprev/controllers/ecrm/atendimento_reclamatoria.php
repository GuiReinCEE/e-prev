<?php
class atendimento_reclamatoria extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_empresa="", $cd_registro_empregado="", $seq_dependencia="")
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;
			$this->load->view('ecrm/atendimento_reclamatoria/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function detalhe($cd_atendimento_reclamatoria = 0 ,$cd_empresa="", $cd_registro_empregado="", $seq_dependencia="", $cd_atendimento = "")
    {
		CheckLogin();
		$this->load->model('projetos/Atendimento_reclamatoria_model');
		$this->load->model('projetos/Atendimento_reclamatoria_retorno_model');
		$result = null;
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{
			if(intval($cd_atendimento_reclamatoria) > 0)
			{
				$data = $this->Atendimento_reclamatoria_model->carregar($cd_atendimento_reclamatoria);
				
				
				$this->Atendimento_reclamatoria_retorno_model->listar($result, Array('cd_atendimento_reclamatoria'=>$cd_atendimento_reclamatoria));
				$data['collection'] = $result->result_array();				
			}
			else
			{
				$data['cd_atendimento_reclamatoria'] = $cd_atendimento_reclamatoria;
				$data['cd_empresa']                  = $cd_empresa;
				$data['cd_registro_empregado']       = $cd_registro_empregado;
				$data['seq_dependencia']             = $seq_dependencia;
				$data['cd_atendimento']              = $cd_atendimento;
				$data['observacao']                  = "";
				$data['dt_encerrado']                = "";
				$data['collection'] = Array();	
			}
			
			$this->load->view('ecrm/atendimento_reclamatoria/detalhe.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Atendimento_reclamatoria_model');

		
        $data['collection'] = array();
        $result = null;

		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{
			// --------------------------
			// filtros ...

			$args=array();

			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["dt_ini"]                = $this->input->post("dt_ini", TRUE);
			$args["dt_fim"]                = $this->input->post("dt_fim", TRUE);

			// --------------------------
			// listar ...

			$this->Atendimento_reclamatoria_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('ecrm/atendimento_reclamatoria/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }


	function salvar()
	{
		CheckLogin();
		
		$this->load->model('projetos/Atendimento_reclamatoria_model');
		
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{			
			$dados['cd_atendimento_reclamatoria'] = $this->input->post("cd_atendimento_reclamatoria",TRUE);
			$dados['cd_empresa']                  = $this->input->post("cd_empresa",TRUE);
			$dados['cd_registro_empregado']       = $this->input->post("cd_registro_empregado",TRUE);
			$dados['seq_dependencia']             = $this->input->post("seq_dependencia",TRUE);
			$dados['cd_atendimento']              = $this->input->post("cd_atendimento",TRUE);
			$dados['observacao']                  = $this->input->post("observacao",TRUE);
			$dados['cd_usuario_inclusao']         = usuario_id();
			
			$cd = $this->Atendimento_reclamatoria_model->salvar($dados);

			redirect("ecrm/atendimento_reclamatoria/detalhe/".$cd, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			

	}	
		
    function retorno($cd_atendimento_reclamatoria_retorno = 0, $cd_atendimento_reclamatoria = 0)
    {
		CheckLogin();
		$this->load->model('projetos/Atendimento_reclamatoria_retorno_model');
		
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{
			if(intval($cd_atendimento_reclamatoria_retorno) > 0)
			{
				$data = $this->Atendimento_reclamatoria_retorno_model->carregar($cd_atendimento_reclamatoria_retorno);

			}
			else
			{
				$data['cd_atendimento_reclamatoria']         = $cd_atendimento_reclamatoria;
				$data['cd_atendimento_reclamatoria_retorno'] = $cd_atendimento_reclamatoria_retorno;
				$data['observacao']                          = "";
			}
			
			$this->load->view('ecrm/atendimento_reclamatoria/retorno.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function retorno_salvar()
	{
		CheckLogin();
		
		$this->load->model('projetos/Atendimento_reclamatoria_retorno_model');
		
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{			
			$dados['cd_atendimento_reclamatoria_retorno'] = $this->input->post("cd_atendimento_reclamatoria_retorno",TRUE);
			$dados['cd_atendimento_reclamatoria']         = $this->input->post("cd_atendimento_reclamatoria",TRUE);
			$dados['observacao']                          = $this->input->post("observacao",TRUE);
			$dados['cd_usuario_inclusao']                 = usuario_id();
			
			$cd = $this->Atendimento_reclamatoria_retorno_model->salvar($dados);

			redirect("ecrm/atendimento_reclamatoria/retorno/".$cd."/".$this->input->post("cd_atendimento_reclamatoria",TRUE), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			

	}

	function encerra()
	{
		CheckLogin();
		
		$this->load->model('projetos/Atendimento_reclamatoria_model');
		
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{			
			$dados['cd_atendimento_reclamatoria'] = $this->input->post("cd_atendimento_reclamatoria",TRUE);
			$dados['cd_usuario_encerrado']         = usuario_id();
			
			$this->Atendimento_reclamatoria_model->encerra($dados);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			

	}	
	
    function acompanhamento($cd_atendimento_reclamatoria = 0)
    {
		CheckLogin();
		$this->load->model('projetos/Atendimento_reclamatoria_model');
		$result = null;
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{
			if(intval($cd_atendimento_reclamatoria) > 0)
			{
				$data = $this->Atendimento_reclamatoria_model->carregar($cd_atendimento_reclamatoria);
				$data['observacao'] = "";
				
				$this->Atendimento_reclamatoria_model->acompanhamento($result, Array('cd_atendimento_reclamatoria'=>$cd_atendimento_reclamatoria));
				$data['collection'] = $result->result_array();				
			}
			else
			{
				$data['cd_atendimento_reclamatoria'] = $cd_atendimento_reclamatoria;
				$data['observacao'] = "";
				$data['collection'] = Array();	
			}
			
			$this->load->view('ecrm/atendimento_reclamatoria/acompanhamento.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvarAcompanhamento()
	{
		CheckLogin();
		
		$this->load->model('projetos/Atendimento_reclamatoria_model');
		
		if(gerencia_in(array('GAP','GA','GJ','GB')))
		{			
			$dados['cd_atendimento_reclamatoria'] = $this->input->post("cd_atendimento_reclamatoria",TRUE);
			$dados['observacao']                  = $this->input->post("observacao",TRUE);
			$dados['cd_usuario_inclusao']         = usuario_id();
			
			$this->Atendimento_reclamatoria_model->salvarAcompanhamento($dados);

			redirect("ecrm/atendimento_reclamatoria/acompanhamento/".$this->input->post("cd_atendimento_reclamatoria",TRUE), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			

	}

	public function anexo($cd_atendimento_reclamatoria)
    {
		CheckLogin();
	
		if(gerencia_in(array('GAP','GJ', 'GCM')))
		{
			$this->load->model('projetos/atendimento_reclamatoria_model');

			$data['cd_atendimento_reclamatoria'] = $cd_atendimento_reclamatoria;

			$data['collection'] = $this->atendimento_reclamatoria_model->anexo($cd_atendimento_reclamatoria);
			
			$this->load->view('ecrm/atendimento_reclamatoria/arquivo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function salvar_anexo()
    {
        CheckLogin();
	
		if(gerencia_in(array('GAP','GJ', 'GCM')))
		{
			$this->load->model('projetos/atendimento_reclamatoria_model');

        	$cd_atendimento_reclamatoria = $this->input->post('cd_atendimento_reclamatoria', TRUE);

            $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args = array();        

                    $args['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                    $args['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                    $args['cd_usuario']   = $this->session->userdata('codigo');      
                    
                    $this->atendimento_reclamatoria_model->salvar_anexo(intval($cd_atendimento_reclamatoria), $args);
                    
                    $nr_conta++;
                }
            }

            redirect('ecrm/atendimento_reclamatoria/anexo/'.intval($cd_atendimento_reclamatoria), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_anexo($cd_atendimento_reclamatoria, $cd_atendimento_reclamatoria_arquivo)
	{
		CheckLogin();
	
		if(gerencia_in(array('GAP','GJ', 'GCM')))
		{
			$this->load->model('projetos/atendimento_reclamatoria_model');

			$this->atendimento_reclamatoria_model->excluir_anexo($cd_atendimento_reclamatoria_arquivo, $this->session->userdata('codigo'));
		
			redirect('ecrm/atendimento_reclamatoria/anexo/'.intval($cd_atendimento_reclamatoria), 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}
