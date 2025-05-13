<?php
class relatorio_auditoria extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('gestao/relatorio_auditoria_model');
    }

    private function get_permissao()
    {
        #Comitê de Qualidade
        if($this->session->userdata('indic_12') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('codigo') == '26')
        {
            return TRUE;
        }
        else if($this->session->userdata('codigo') == '78')
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
        $args = Array();
		$data = Array();
		$result = null;
        
        $this->relatorio_auditoria_model->get_auditor_relatorios( $result, $args );
        $data['arr_auditor'] = $result->result_array();
        
        $this->relatorio_auditoria_model->get_processos_relatorios( $result, $args );
        $data['arr_processo'] = $result->result_array();
        
        $this->relatorio_auditoria_model->get_equipe_relatorios( $result, $args );
        $data['arr_equipe'] = $result->result_array();

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/index.php',$data);
    }
    
    function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;

        $args["ano"]               = $this->input->post("ano", TRUE);
        $args["cd_auditor_lider"]  = $this->input->post("cd_auditor_lider", TRUE);
        $args["cd_processo"]       = $this->input->post("cd_processo", TRUE);
        $args["cd_usuario_equipe"] = $this->input->post("cd_usuario_equipe", TRUE);
        $args["tipo"]              = $this->input->post("tipo", TRUE);
        $args["fl_impacto"]        = $this->input->post("fl_impacto", TRUE);
        $args["fl_tipo"]           = $this->input->post("fl_tipo", TRUE);
        
        if(($args["tipo"]  == 'N') OR ($args["tipo"]  == 'M'))
        {
            $args["fl_impacto"] = 'N';
        }

        manter_filtros($args);
 
        $this->relatorio_auditoria_model->listar( $result, $args );

        $data['collection'] = $result->result_array();
        
        foreach($data['collection'] as $item)
        {
            $args = Array();
            
            $args['cd_relatorio_auditoria'] = $item['cd_relatorio_auditoria'];
            
            $this->relatorio_auditoria_model->processo_checked($result, $args);
            
            $data['ar_processo'][$item['cd_relatorio_auditoria']] = $result->result_array();
            
            $this->relatorio_auditoria_model->equipe_checked($result, $args);
            
            $data['ar_equipe'][$item['cd_relatorio_auditoria']] = $result->result_array();
        }

        $data['fl_permissao'] = $this->get_permissao();
     
        $this->load->view('gestao/relatorio_auditoria/index_result', $data);
    }
    
    function cadastro($cd_relatorio_auditoria = 0)
    {
        $args = Array();
		$data = Array();
		$result = null;
        $data['ar_processos_checked'] = Array();
        
        $conclusao = "A concepção do sistema de gestão e a documentação apresentada atendem plenamente ao
planejamento e estão adequados ao modelo de referência. A estruturação do Manual de
Gestão com base em processos facilita o entendimento do funcionamento da organização.
Durante a execução da auditoria foram constatadas no sistema de gestão da qualidade da
organização não conformidades e observações conforme quadro abaixo.
A equipe de auditoria concluiu que o atual sistema de gestão da organização auditado de
forma amostral possui as condições necessárias para atender com eficácia à política da
qualidade, aos objetivos da qualidade e aos requisitos da NBR ISO 9001:2008.";
        
        $args['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        $data['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        
        $this->relatorio_auditoria_model->get_processos( $result, $args );
        $data['ar_processos'] = $result->result_array();
        
        $this->relatorio_auditoria_model->get_usuarios_comite( $result, $args );
        $data['ar_comite'] = $result->result_array();
		
		$data['collection'] = array();
        
        if($data['cd_relatorio_auditoria'] == 0)
        {
            $data['row'] = Array(
				'cd_relatorio_auditoria' => 0,
				'mes_ano'                => '',
				'escopo'                 => '',
				'cd_auditor_lider'       => '106',
				'representante'          => '',
				'conclusao'              => $conclusao,
                'fl_tipo'                => '',
                'ds_empresa'             => ''
			);
        }
        else
        {
            $this->relatorio_auditoria_model->carrega( $result, $args );
            $data['row'] = $result->row_array();
            
            $this->relatorio_auditoria_model->get_processos_checked( $result, $args );
            $ar_processos_checked = $result->result_array();
			
            foreach($ar_processos_checked as $item)
			{
				$data['ar_processos_checked'][] = $item['cd_processo'];
            }
			
			$this->relatorio_auditoria_model->total_constatacao( $result, $args );
			$data['collection'] = $result->row_array();
        }

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/cadastro', $data);
    }
    
    function salvar()
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria"] = $this->input->post("cd_relatorio_auditoria", TRUE);
            $args["nr_mes"]                 = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]                 = $this->input->post("nr_ano", TRUE);
            $args["escopo"]                 = $this->input->post("escopo", TRUE);
            $args["representante"]          = $this->input->post("representante", TRUE);
            $args["ar_processos"]           = $this->input->post("ar_processos", TRUE);
            $args["cd_auditor_lider"]       = $this->input->post("cd_auditor_lider", TRUE);
            $args["conclusao"]              = $this->input->post("conclusao", TRUE);
            $args["fl_tipo"]                = $this->input->post("fl_tipo", TRUE);
            $args["ds_empresa"]             = $this->input->post("ds_empresa", TRUE);
            $args["cd_usuario_inclusao"]    = $this->session->userdata('codigo');
            
            $cd_relatorio_auditoria = $this->relatorio_auditoria_model->salvar($result, $args);
    		redirect("gestao/relatorio_auditoria/cadastro/".$cd_relatorio_auditoria, "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function excluir_processo($cd_relatorio_auditoria)
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria"] = $cd_relatorio_auditoria;
            $args["cd_usuario_exclusao"]    = $this->session->userdata('codigo');
            
            $this->relatorio_auditoria_model->excluir_processo($result, $args);
            
            redirect("gestao/relatorio_auditoria", "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function equipe($cd_relatorio_auditoria)
    {
        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        $data['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        
        $this->relatorio_auditoria_model->get_usuarios_comite( $result, $args );
        $data['ar_usuarios'] = $result->result_array();
        
        $this->relatorio_auditoria_model->equipe_checked( $result, $args );
        $data['collection'] = $result->result_array();

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/equipe', $data);
    }
    
    function salvar_equipe()
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria"]        = $this->input->post("cd_relatorio_auditoria", TRUE);
            $args["cd_relatorio_auditoria_equipe"] = $this->input->post("cd_relatorio_auditoria_equipe", TRUE);
            $args["cd_usuario"]                    = $this->input->post("cd_usuario", TRUE);
            $args["tipo"]                          = $this->input->post("tipo", TRUE);
            $args["cd_usuario_inclusao"]           = $this->session->userdata('codigo');
            
            $this->relatorio_auditoria_model->salvar_equipe($result, $args);
    		
    		redirect("gestao/relatorio_auditoria/equipe/".$args["cd_relatorio_auditoria"], "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function excluir_equipe($cd_relatorio_auditoria_equipe, $cd_relatorio_auditoria)
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria_equipe"] = $cd_relatorio_auditoria_equipe;
            $args["cd_usuario_exclusao"]           = $this->session->userdata('codigo');
            
            $this->relatorio_auditoria_model->excluir_equipe($result, $args);
            
            redirect("gestao/relatorio_auditoria/equipe/".$cd_relatorio_auditoria, "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function carrega_equipe()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_relatorio_auditoria_equipe"] = $this->input->post("cd_relatorio_auditoria_equipe", TRUE); 
        
        $this->relatorio_auditoria_model->carrega_equipe( $result, $args );
        
        $data = array_map("arrayToUTF8", $result->row_array());			
	    echo json_encode($data);
    }
    
    function constatacao($cd_relatorio_auditoria)
    {
        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        $data['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);

        $this->relatorio_auditoria_model->carrega($result, $args);
        $data['relatorio'] = $result->row_array();
        
        $this->relatorio_auditoria_model->processo_constatacao($result, $args);
        $data['ar_processo'] = $result->result_array();
        
        $this->relatorio_auditoria_model->lista_constatacao( $result, $args );
        $data['collection'] = $result->result_array();

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/constatacao', $data);
    }
    
    function salvar_constatacao()
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria"]             = $this->input->post("cd_relatorio_auditoria", TRUE);
            $args["cd_relatorio_auditoria_constatacao"] = $this->input->post("cd_relatorio_auditoria_constatacao", TRUE);
            $args["relato"]                             = $this->input->post("relato", TRUE);
            $args["cd_processo"]                        = $this->input->post("cd_processo", TRUE);
            $args["evidencias"]                         = $this->input->post("evidencias", TRUE);
            $args["tipo"]                               = $this->input->post("tipo", TRUE);
            $args["fl_impacto"]                         = $this->input->post("fl_impacto", TRUE);
            $args["nr_ano_nc"]                          = $this->input->post("nr_ano_nc", TRUE);
            $args["nr_nc"]                              = $this->input->post("nr_nc", TRUE);
            $args["cd_usuario_inclusao"]                = $this->session->userdata('codigo');
    		
            if(($args["tipo"]  == 'N') OR ($args["tipo"]  == 'M'))
            {
                $args["fl_impacto"] = 'N';
            }
            
            $this->relatorio_auditoria_model->salvar_constatacao($result, $args);
    		redirect("gestao/relatorio_auditoria/constatacao/".$args["cd_relatorio_auditoria"], "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function concluir_constatacao($cd_relatorio_auditoria)
    {
        if($this->get_permissao())
        {
            $this->relatorio_auditoria_model->concluir_constatacao(intval($cd_relatorio_auditoria), $this->session->userdata('codigo'));

            redirect('gestao/relatorio_auditoria/constatacao/'.$cd_relatorio_auditoria, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function excluir_constatacao($cd_relatorio_auditoria_constatacao, $cd_relatorio_auditoria)
    {
        if($this->get_permissao())
        {
            $args = Array();
    		$data = Array();
    		$result = null;
            
            $args["cd_relatorio_auditoria_constatacao"] = $cd_relatorio_auditoria_constatacao;
            $args["cd_usuario_exclusao"]                = $this->session->userdata('codigo');
            
            $this->relatorio_auditoria_model->excluir_constatacao($result, $args);
            
            redirect("gestao/relatorio_auditoria/constatacao/".$cd_relatorio_auditoria, "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function carrega_constatacao()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_relatorio_auditoria_constatacao"] = $this->input->post("cd_relatorio_auditoria_constatacao", TRUE); 
        
        $this->relatorio_auditoria_model->carrega_constatacao( $result, $args );
        
        $data = array_map("arrayToUTF8", $result->row_array());			
	    echo json_encode($data);
    }
	
	function acompanhamento($cd_relatorio_auditoria)
    {
        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        $data['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        
        $this->relatorio_auditoria_model->lista_acompanhamento( $result, $args );
        $data['collection'] = $result->result_array();

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/acompanhamento', $data);
    }
	
	function salvar_acompanhamento()
	{
        if($this->get_permissao())
        {
    		$args = Array();
    		$data = Array();
    		$result = null;
    		
    		$args['cd_relatorio_auditoria']     = $this->input->post("cd_relatorio_auditoria", TRUE);
    		$args['descricao']                  = $this->input->post("descricao", TRUE);
    		$args['cd_usuario']                 = $this->session->userdata('codigo');
    		
    		$this->relatorio_auditoria_model->salvar_acompanhamento($result, $args);
    		
    		redirect("gestao/relatorio_auditoria/acompanhamento/".$args['cd_relatorio_auditoria'], "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function excluir_acompanhamento($cd_relatorio_auditoria, $cd_relatorio_auditoria_acompanhamento)
	{
        if($this->get_permissao())
        {
    		$args = Array();
    		$data = Array();
    		$result = null;
    		
    		$args['cd_relatorio_auditoria']                = $cd_relatorio_auditoria;
    		$args['cd_relatorio_auditoria_acompanhamento'] = $cd_relatorio_auditoria_acompanhamento;
    		$args['cd_usuario']                            = $this->session->userdata('codigo');
    		
    		$this->relatorio_auditoria_model->excluir_acompanhamento($result, $args);
    		
    		redirect("gestao/relatorio_auditoria/acompanhamento/" . $args['cd_relatorio_auditoria'], "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	function anexo($cd_relatorio_auditoria)
    {
        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        $data['cd_relatorio_auditoria'] = intval($cd_relatorio_auditoria);
        
        $this->relatorio_auditoria_model->lista_anexo( $result, $args );
        $data['collection'] = $result->result_array();

        $data['fl_permissao'] = $this->get_permissao();
        
        $this->load->view('gestao/relatorio_auditoria/anexo', $data);
    }
	
	function salvar_anexo()
	{
        if($this->get_permissao())
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
    				
    				$args['arquivo_nome']           = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
    				$args['arquivo']                = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
    				$args['cd_relatorio_auditoria'] = $this->input->post("cd_relatorio_auditoria", TRUE);
    				$args["cd_usuario"]             = $this->session->userdata('codigo');
    				
    				$this->relatorio_auditoria_model->salvar_anexo($result, $args);
    				
    				$nr_conta++;
    			}
    		}
    		
    		redirect("gestao/relatorio_auditoria/anexo/".intval($args["cd_relatorio_auditoria"]), "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function excluir_anexo($cd_relatorio_auditoria, $cd_relatorio_auditoria_anexo)
	{
        if($this->get_permissao())
        {
    		$result = null;
    		$data = Array();
    		$args = Array();
    		
    		$args['cd_relatorio_auditoria']       = $cd_relatorio_auditoria;
    		$args['cd_relatorio_auditoria_anexo'] = $cd_relatorio_auditoria_anexo;
    		$args["cd_usuario"]                   = $this->session->userdata('codigo');

    		$this->relatorio_auditoria_model->excluir_anexo($result, $args);
    		
    		redirect("gestao/relatorio_auditoria/anexo/".intval($args["cd_relatorio_auditoria"]), "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
    
    function gera_pdf($cd_relatorio_auditoria)
    {
        $this->load->plugin('fpdf');

        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_relatorio_auditoria'] = $cd_relatorio_auditoria;
        
        $this->relatorio_auditoria_model->lista_pdf( $result, $args );
        $collection = $result->row_array();
        
        $this->relatorio_auditoria_model->processo_constatacao($result, $args);
            
        $ar_processos_checked = $result->result_array();
        
        $this->relatorio_auditoria_model->equipe_checked( $result, $args );
        $arr_equipe = $result->result_array();
        
        $this->relatorio_auditoria_model->lista_constatacao( $result, $args );
        $arr_constatacao = $result->result_array();
		
		$this->relatorio_auditoria_model->lista_acompanhamento( $result, $args );
        $arr_acompanhamento = $result->result_array();
		
		$this->relatorio_auditoria_model->total_constatacao( $result, $args );
		$arr = $result->row_array();
        
        $this->relatorio_auditoria_model->lista_anexo( $result, $args );
        $ar_anexo = $result->result_array();		

        $ob_pdf = new PDF();
		
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');

		#### INCLUI ANEXOS NO PDF ####
		if(count($ar_anexo) > 0)
		{
			foreach($ar_anexo as $ar_item_anexo)
			{
				$ob_pdf->Attach("./up/relatorio_auditoria_anexo/".$ar_item_anexo["arquivo"], $ar_item_anexo["arquivo_nome"]);
			}		
			#### EXIBIR ABA DE ANEXOS NO PDF ###
			$ob_pdf->OpenAttachmentPane();
		}
		
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10,14,5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Relatório de Auditoria";
        
        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0,0,0);

        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, 4.5, "ORGANIZAÇÃO AUDITADA",0,"C");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont( 'segoeuil','', 10 );
        $ob_pdf->MultiCell(190, 4.5, "Razão Social: Fundação CEEE de Seguridade Social - ELETROCEEE");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Endereço: Rua dos Andradas     Número 702");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "CEP: 90020-004                 Cidade: Porto Alegre                  Estado: RS");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "CNPJ: 90884412/0001-24         Inscrição Estadual: Isento");
        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetFont( 'segoeuib','', 12 );
        $ob_pdf->MultiCell(190, 4.5, "IDENTIFICAÇÃO DA AUDITORIA",0,"C");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont( 'segoeuil','', 10 );
        $ob_pdf->MultiCell(190, 4.5, "Período da Auditoria: ". $collection['ano_mes'] . "   Critério de Auditoria: NBR ISO 9001");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Escopo: ".$collection['escopo']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        
        $ob_pdf->SetWidths(array(192));
        $ob_pdf->SetAligns(array('L'));
        $ob_pdf->SetFont('segoeuil','',10);
        
        $ob_pdf->Row(array('Processos Auditados:'));
        
        foreach($ar_processos_checked as $item)
        {
             $ob_pdf->Row(array($item['text']));
        }
        
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Representantes da Organização: ".$collection['representante']);
        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetFont( 'segoeuib','', 12 );
        $ob_pdf->MultiCell(190, 4.5, "EQUIPE AUDITORA",0,"C");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont( 'segoeuil','', 10 );
        if(trim($collection['fl_tipo']) == 'I')
        {
            //$ob_pdf->MultiCell(190, 4.5, "Auditor Lider: ".$collection['auditor_lider']);
        }
        else
        {
            $ob_pdf->MultiCell(190, 4.5, "Empresa: ".$collection['ds_empresa']);
        }
        
        $ob_pdf->SetWidths(array(113,79));
        $ob_pdf->SetAligns(array('C', 'C'));
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->Row(array("Usuário", "Tipo"));
        $ob_pdf->SetAligns(array('L', 'L'));
        
        foreach($arr_equipe as $item)
        {
             $ob_pdf->Row(array($item['nome'], $item['tipo']));
        }
        
        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetFont( 'segoeuib','', 12 );
        $ob_pdf->MultiCell(190, 4.5, "CONCLUSÃO DA EQUIPE AUDITORA",0,"C");
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->SetFont( 'segoeuil','', 10 );
        $ob_pdf->MultiCell(190, 4.5, $collection['conclusao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 4);
		$ob_pdf->SetWidths(array(113,79));
        $ob_pdf->SetAligns(array('C', 'C'));
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->Row(array("Constatação", "Quantidade"));
		$ob_pdf->SetAligns(array('L', 'C'));
		$ob_pdf->Row(array("Não Conformidade", $arr['tl_nao_conformidade']));
		$ob_pdf->Row(array("Oportunidade de Melhoria", $arr['tl_melhoria']));
		$ob_pdf->Row(array("Observação", $arr['tl_observacao']));
		
        if(count($arr_constatacao) > 0)
        {
            $nr_conta = 1;
            $ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, 4.5, "CONSTATAÇÕES",0,"C");	
			$ob_pdf->SetY($ob_pdf->GetY() + 4);				
            
            foreach($arr_constatacao as $item)
            {
                #print_r($item); exit;
				
				$ob_pdf->SetWidths(array(192));
                $ob_pdf->SetAligns(array('L'));
                $ob_pdf->SetFont('segoeuib','',10);

                $ob_pdf->Row(array('CONSTATAÇÃO Nº '.$nr_conta. ':'.'                 ['.(trim($item['cd_tipo'])== 'N' ? 'X' : ' ').'] NÃO CONFORMIDADE   ['.(trim($item['cd_tipo'])== 'O' ? 'X' : ' ').'] OBSERVAÇÃO  ['.(trim($item['cd_tipo'])== 'M' ? 'X' : ' ').'] OPORTUNIDADE DE MELHORIA'));
                $ob_pdf->SetFont('segoeuil','',10);
                
                $ob_pdf->Row(array('Relato: '.$item['relato']));
                
                $ob_pdf->Row(array('Processo: '.$item['procedimento']));
                
				if(trim($item['cd_tipo']) != 'M')
				{
					$ob_pdf->Row(array('Evidências: '.$item['evidencias']));
				}
                
                if(trim($item['tipo'])== 'O')
				{
                    $ob_pdf->Row(array('Impacto significativo: '.$item['fl_impacto']));
                }
				
				$ob_pdf->Row(array('Usuário: '.$item['usuario_alteracao']));
				$ob_pdf->Row(array('Dt Alteração: '.$item['dt_alteracao']));
				
                $ob_pdf->SetY($ob_pdf->GetY() + 4);
                $nr_conta ++;
            }
        }
		
		if(count($arr_acompanhamento) > 0)
        {
            $ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, 4.5, "REGISTRO GERAIS",0,"C");		
			$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
			$ob_pdf->SetWidths(array(35, 100, 57));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->SetFont('segoeuib','',10);
			$ob_pdf->Row(array('Dt Registro', 'Descrição', 'Usuário'));
			$ob_pdf->SetAligns(array('C', 'L', 'L'));
			$ob_pdf->SetFont('segoeuil','',10);
			
			foreach($arr_acompanhamento as $item)
            {
				$ob_pdf->Row(array(
					$item['dt_inclusao'],
					$item['descricao'],
					$item['nome']
				));
			}
		}
		
		if(count($ar_anexo) > 0)
        {
            $ob_pdf->AddPage();
			$ob_pdf->SetFont('segoeuib', '', 12);
			$ob_pdf->MultiCell(190, 4.5, "ANEXOS",0,"C");	
			$ob_pdf->SetY($ob_pdf->GetY() + 4);			
			
			$ob_pdf->SetWidths(array(35, 100, 57));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->SetFont('segoeuib','',10);
			$ob_pdf->Row(array('Dt Anexo', 'Descrição', 'Usuário'));
			$ob_pdf->SetAligns(array('C', 'L', 'L'));
			$ob_pdf->SetFont('segoeuil','',10);
			
			foreach($ar_anexo as $item)
            {
				$ob_pdf->Row(array(
					$item['dt_inclusao'],
					$item['arquivo_nome'],
					$item['nome']
				));
			}
		}		
        
        $ob_pdf->Output();
        exit;
    }
}
?>