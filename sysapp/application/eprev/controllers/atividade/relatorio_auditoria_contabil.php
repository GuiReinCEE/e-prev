<?php

class Relatorio_auditoria_contabil extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/relatorio_auditoria_contabil_model');
	}
    
    function index()
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $this->load->view('atividade/relatorio_auditoria_contabil/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function listar()
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
            $args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);
            $args["dt_envio_ini"]    = $this->input->post("dt_envio_ini", TRUE);
            $args["dt_envio_fim"]    = $this->input->post("dt_envio_fim", TRUE);
            
            manter_filtros($args);
            
            $this->relatorio_auditoria_contabil_model->listar($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function cadastro($cd_relatorio_auditoria_contabil = 0)
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args['cd_relatorio_auditoria_contabil'] = intval($cd_relatorio_auditoria_contabil);
            
            if (intval($args['cd_relatorio_auditoria_contabil']) == 0)
            {
                $data['row'] = Array(
                    'cd_relatorio_auditoria_contabil' => $args['cd_relatorio_auditoria_contabil'],
                    'ds_relatorio_auditoria_contabil' => '',
                    'arquivo'                         => '',
                    'arquivo_nome'                    => '',
                    'dt_envio_gc'                     => '',
                    'ano_numero'                      => '',
                    'qt_itens'                        => '',
                    'qt_itens_enviado'                => '',
                    'dt_envio_sg'                     => '',
                    'dt_alchemy'                      => '',
                    'dt_aprovado'                     => ''
                );
            }
            else
            {
                $this->relatorio_auditoria_contabil_model->cadastro($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('atividade/relatorio_auditoria_contabil/cadastro', $data); 
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function salvar()
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_relatorio_auditoria_contabil"] = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);
            $args["ds_relatorio_auditoria_contabil"] = $this->input->post("ds_relatorio_auditoria_contabil", TRUE);
            $args["dt_envio_sg"]                     = $this->input->post("dt_envio_sg", TRUE);
            $args["dt_alchemy"]                      = $this->input->post("dt_alchemy", TRUE);
            $args["arquivo"]                         = $this->input->post("arquivo", TRUE);
            $args["arquivo_nome"]                    = $this->input->post("arquivo_nome", TRUE);  
            $args["cd_usuario"]                      = $this->session->userdata("codigo");
            
            $cd_relatorio_auditoria_contabil = $this->relatorio_auditoria_contabil_model->salvar($result, $args);
            
            redirect("atividade/relatorio_auditoria_contabil/cadastro/".$cd_relatorio_auditoria_contabil, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function enviar_gc($cd_relatorio_auditoria_contabil)
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"] = intval($cd_relatorio_auditoria_contabil);
            $args["cd_usuario"]                      = $this->session->userdata("codigo");
            
            $this->relatorio_auditoria_contabil_model->enviar_gc($result, $args);
            
            redirect("atividade/relatorio_auditoria_contabil/cadastro/".$args["cd_relatorio_auditoria_contabil"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function itens($cd_relatorio_auditoria_contabil)
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"] = intval($cd_relatorio_auditoria_contabil);
            
            $this->relatorio_auditoria_contabil_model->cadastro($result, $args);
            $data['row'] = $result->row_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/itens', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function listar_itens()
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"] = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);
            
            $this->relatorio_auditoria_contabil_model->listar_itens($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/itens_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function excluir_item($cd_relatorio_auditoria_contabil, $cd_relatorio_auditoria_contabil_item)
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil_item"] = $cd_relatorio_auditoria_contabil_item;
            $args["cd_usuario"]                           = $this->session->userdata("codigo");
            
            $this->relatorio_auditoria_contabil_model->excluir_item($result, $args);

            redirect('atividade/relatorio_auditoria_contabil/itens/'.intval($cd_relatorio_auditoria_contabil), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_item()
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"]      = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);
            $args["nr_numero_item"]                       = $this->input->post("nr_numero_item", TRUE);
            $args["ds_relatorio_auditoria_contabil_item"] = $this->input->post("ds_relatorio_auditoria_contabil_item", TRUE);
            $args["cd_usuario_responsavel"]               = $this->input->post("cd_usuario_responsavel", TRUE);
            $args["cd_usuario_substituto"]                = $this->input->post("cd_usuario_substituto", TRUE);
            $args["dt_limite"]                            = $this->input->post("dt_limite", TRUE);
            $args["cd_usuario"]                           = $this->session->userdata("codigo");
            
            $this->relatorio_auditoria_contabil_model->salvar_item($result, $args);
            
            redirect("atividade/relatorio_auditoria_contabil/itens/".intval($args["cd_relatorio_auditoria_contabil"]), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function enviar($cd_relatorio_auditoria_contabil)
    {
        if(gerencia_in(array('SG', 'GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"] = intval($cd_relatorio_auditoria_contabil);
            $args["cd_usuario"]                      = $this->session->userdata("codigo");
            
            $this->relatorio_auditoria_contabil_model->enviar($result, $args);
            
            redirect("atividade/relatorio_auditoria_contabil/itens/".$args["cd_relatorio_auditoria_contabil"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function encaminhar_aprovacao($cd_relatorio_auditoria_contabil)
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();
            
            $args["cd_relatorio_auditoria_contabil"] = $cd_relatorio_auditoria_contabil;
            $args['cd_usuario']                      = $this->session->userdata('codigo');
            $args["acompanhamento"]                  = "Encaminhado para Aprovação";
            
            $this->relatorio_auditoria_contabil_model->encaminhar_aprovacao($result, $args);
            
            $this->relatorio_auditoria_contabil_model->salvar_acompanhamento($result, $args);
            
            redirect("atividade/relatorio_auditoria_contabil/itens/".$args["cd_relatorio_auditoria_contabil"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function minhas()
    {
        $result = null;
        $data = Array();
        $args = Array();

        $this->load->view('atividade/relatorio_auditoria_contabil/minhas', $data);
	}
    
    function listar_minhas()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args["dt_limite_ini"]   = $this->input->post("dt_limite_ini", TRUE);
        $args["dt_limite_fim"]   = $this->input->post("dt_limite_fim", TRUE);
        $args["dt_envio_ini"]    = $this->input->post("dt_envio_ini", TRUE);
        $args["dt_envio_fim"]    = $this->input->post("dt_envio_fim", TRUE);
        $args["dt_resposta_ini"] = $this->input->post("dt_resposta_ini", TRUE);
        $args["dt_resposta_fim"] = $this->input->post("dt_resposta_fim", TRUE);
        $args["fl_resposta"]     = $this->input->post("fl_resposta", TRUE);
        $args['cd_usuario']      = $this->session->userdata('codigo');
        
        manter_filtros($args);
        
        $this->relatorio_auditoria_contabil_model->listar_minhas($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('atividade/relatorio_auditoria_contabil/minhas_result', $data);
    }
    
    function resposta($cd_relatorio_auditoria_contabil_item)
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_relatorio_auditoria_contabil_item'] = $cd_relatorio_auditoria_contabil_item;
        $args['cd_usuario']                           = $this->session->userdata('codigo');
        
        $this->relatorio_auditoria_contabil_model->resposta($result, $args);
        $data['row'] = $result->row_array();
        
        $this->load->view('atividade/relatorio_auditoria_contabil/resposta', $data);
    }
    
    function salvar_resposta()
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_relatorio_auditoria_contabil_item'] = $this->input->post("cd_relatorio_auditoria_contabil_item", TRUE);
        $args['arquivo']                              = $this->input->post("arquivo", TRUE);
        $args['arquivo_nome']                         = $this->input->post("arquivo_nome", TRUE);
        $args['ds_resposta']                          = $this->input->post("ds_resposta", TRUE);
        $args['cd_usuario']                           = $this->session->userdata('codigo');
        
        $this->relatorio_auditoria_contabil_model->salvar_resposta($result, $args);
        
        redirect("atividade/relatorio_auditoria_contabil/resposta/".$args["cd_relatorio_auditoria_contabil_item"], "refresh");
    }
    
    function confimar($cd_relatorio_auditoria_contabil_item)
    {
        $result = null;
        $data = Array();
        $args = Array();
        
        $args['cd_relatorio_auditoria_contabil_item'] = $cd_relatorio_auditoria_contabil_item;
        $args['cd_usuario']                           = $this->session->userdata('codigo');
        
        $this->relatorio_auditoria_contabil_model->confimar($result, $args);
        
        redirect("atividade/relatorio_auditoria_contabil/minhas", "refresh");
    }
    
    function anexo($cd_relatorio_auditoria_contabil)
    {       
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_relatorio_auditoria_contabil'] = $cd_relatorio_auditoria_contabil;	

            $this->relatorio_auditoria_contabil_model->cadastro($result, $args);
            $data['row'] = $result->row_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/anexo', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function listar_anexo()
	{
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_relatorio_auditoria_contabil'] = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);

            $this->relatorio_auditoria_contabil_model->listar_anexo($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/anexo_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function salvar_anexo()
	{
        if(gerencia_in(array('GC')))
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

                    $args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
                    $args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);

                    $args['cd_relatorio_auditoria_contabil'] = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);
                    $args["cd_usuario"]                      = $this->session->userdata('codigo');

                    $this->relatorio_auditoria_contabil_model->salvar_anexo($result, $args);

                    $nr_conta++;
                }
            }

            redirect("atividade/relatorio_auditoria_contabil/anexo/".intval($args["cd_relatorio_auditoria_contabil"]), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function excluir_anexo($cd_relatorio_auditoria_contabil, $cd_relatorio_auditoria_contabil_anexo)
	{
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_relatorio_auditoria_contabil_anexo'] = $cd_relatorio_auditoria_contabil_anexo;
            $args["cd_usuario"]                            = $this->session->userdata('codigo');

            $this->relatorio_auditoria_contabil_model->excluir_anexo($result, $args);

            redirect("atividade/relatorio_auditoria_contabil/anexo/".intval($cd_relatorio_auditoria_contabil), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function acompanhamento($cd_relatorio_auditoria_contabil)
    {       
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_relatorio_auditoria_contabil'] = $cd_relatorio_auditoria_contabil;	

            $this->relatorio_auditoria_contabil_model->cadastro($result, $args);
            $data['row'] = $result->row_array();
            
            $this->relatorio_auditoria_contabil_model->listar_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('atividade/relatorio_auditoria_contabil/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}    
    
    function salvar_acompanhamento()
    {
        if(gerencia_in(array('GC')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_relatorio_auditoria_contabil'] = $this->input->post("cd_relatorio_auditoria_contabil", TRUE);
            $args['acompanhamento']                  = $this->input->post("acompanhamento", TRUE);
            $args["cd_usuario"]                      = $this->session->userdata('codigo');

            $this->relatorio_auditoria_contabil_model->salvar_acompanhamento($result, $args);

            redirect("atividade/relatorio_auditoria_contabil/acompanhamento/".intval($cd_relatorio_auditoria_contabil), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function aprovar($cd_relatorio_auditoria_contabil)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_relatorio_auditoria_contabil'] = $cd_relatorio_auditoria_contabil;	
        
        $this->relatorio_auditoria_contabil_model->cadastro($result, $args);
        $data['row'] = $result->row_array();
        
        $this->relatorio_auditoria_contabil_model->listar_anexo($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('atividade/relatorio_auditoria_contabil/aprovar', $data);
    }
    
    function recusar($cd_relatorio_auditoria_contabil)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_relatorio_auditoria_contabil'] = $cd_relatorio_auditoria_contabil;	
        $args["cd_usuario"]                      = $this->session->userdata('codigo');
        $args["acompanhamento"]                  = "Recusado";
        
        $this->relatorio_auditoria_contabil_model->recusar($result, $args);

        $this->relatorio_auditoria_contabil_model->salvar_acompanhamento($result, $args);
        
        redirect("atividade/relatorio_auditoria_contabil/aprovar/".intval($cd_relatorio_auditoria_contabil), "refresh");
    }
    
    function confirmar_aprovacao($cd_relatorio_auditoria_contabil)
    {
        $result = null;
        $data = Array();
        $args = Array();

        $args['cd_relatorio_auditoria_contabil'] = $cd_relatorio_auditoria_contabil;	
        $args["cd_usuario"]                      = $this->session->userdata('codigo');
        $args["acompanhamento"]                  = "Aprovado";
        
        $this->relatorio_auditoria_contabil_model->confirmar_aprovacao($result, $args);

        $this->relatorio_auditoria_contabil_model->salvar_acompanhamento($result, $args);
        
        redirect("atividade/relatorio_auditoria_contabil/aprovar/".intval($cd_relatorio_auditoria_contabil), "refresh");
    }
}

?>