<?php
class nc extends Controller
{

    function __construct()
    {
        parent::Controller();
        
        CheckLogin();

        $this->load->model('projetos/nao_conformidade_model');
    }

    function index()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $this->nao_conformidade_model->comboDiretoria($result, $args);
        $data['diretoria_dd'] = $result->result_array();

        $this->nao_conformidade_model->comboGerencia($result, $args);
        $data['gerencia_dd'] = $result->result_array();

        $this->nao_conformidade_model->comboProcesso($result, $args);
        $data['processo_dd'] = $result->result_array();
        
        $this->nao_conformidade_model->auditores($result, $args);
        $data['arr_auditores'] = $result->result_array();
		
        $this->nao_conformidade_model->comboOrigemEvento($result, $args);
        $data['ar_origem_evento'] = $result->result_array();		
		

        $data['status_dd'][] = array('value' => 'EN', 'text' => 'Sim');
        $data['status_dd'][] = array('value' => 'NE', 'text' => 'Não');

        $data['implementada_dd'][] = array('value' => 'S', 'text' => 'Sim');
        $data['implementada_dd'][] = array('value' => 'N', 'text' => 'Não');

        $data['prorrogada_dd'][] = array('value' => 'S', 'text' => 'Sim');
        $data['prorrogada_dd'][] = array('value' => 'N', 'text' => 'Não');

        $this->load->view('gestao/nc/index', $data);
    }

    function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_usuario_titular"]                = $this->input->post("cd_usuario_titular", TRUE);
        $args["cd_usuario_substituto"]             = $this->input->post("cd_usuario_substituto", TRUE);
        $args["diretoria"]                         = $this->input->post("diretoria", TRUE);
        $args["gerencia"]                          = $this->input->post("gerencia", TRUE);
        $args["cd_nao_conformidade_origem_evento"] = $this->input->post("cd_nao_conformidade_origem_evento", TRUE);
        $args["processo"]                          = $this->input->post("processo", TRUE);
        $args["status"]                            = $this->input->post("status", TRUE);
        $args["implementada"]                      = $this->input->post("implementada", TRUE);
        $args["prorrogada"]                        = $this->input->post("prorrogada", TRUE);
        $args["limite_apre_ac_inicio"]             = $this->input->post("limite_apre_ac_inicio", TRUE);
        $args["limite_apre_ac_fim"]                = $this->input->post("limite_apre_ac_fim", TRUE);
        $args["proposta_inicio"]                   = $this->input->post("proposta_inicio", TRUE);
        $args["proposta_fim"]                      = $this->input->post("proposta_fim", TRUE);
        $args["dt_prop_verif_ini"]                 = $this->input->post("dt_prop_verif_ini", TRUE);
        $args["dt_prop_verif_fim"]                 = $this->input->post("dt_prop_verif_fim", TRUE);
        $args["dt_encerramento_ini"]               = $this->input->post("dt_encerramento_ini", TRUE);
        $args["dt_encerramento_fim"]               = $this->input->post("dt_encerramento_fim", TRUE);
        $args["dt_cadastro_ini"]                   = $this->input->post("dt_cadastro_ini", TRUE);
        $args["dt_cadastro_fim"]                   = $this->input->post("dt_cadastro_fim", TRUE);
        $args["dt_implementacao_ini"]              = $this->input->post("dt_implementacao_ini", TRUE);
        $args["dt_implementacao_fim"]              = $this->input->post("dt_implementacao_fim", TRUE);

        manter_filtros($args);

        $this->nao_conformidade_model->listar($result, $args);
        $data['collection'] = $result->result_array();

        $this->load->view('gestao/nc/partial_result', $data);
    }

    function gerencia_dropdown_ajax()
    {
        if (trim($this->input->post('diretoria')) == '')
        {
            $q = $this->db->query("SELECT DISTINCT(codigo) AS value,nome AS text FROM projetos.divisoes ORDER BY nome");
        }
        else
        {
            $q = $this->db->query("SELECT DISTINCT(codigo) AS value,nome AS text FROM projetos.divisoes WHERE area=? ORDER BY nome", array($this->input->post('diretoria')));
        }

        $r = $q->result_array();

        $options[''] = '::selecione::';
        
        foreach ($r as $i)
        {
            $options[$i['value']] = $i['text'];
        }

        echo form_dropdown("gerencia", $options);
    }

    function cadastro($cd_nao_conformidade = 0)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_nao_conformidade'] = intval($cd_nao_conformidade);
        $data['cd_nao_conformidade'] = intval($cd_nao_conformidade);

        $this->nao_conformidade_model->comboProcesso($result, $args);
        $data['ar_processo'] = $result->result_array();

        if (intval($cd_nao_conformidade) == 0)
        {
            $data['row'] = Array(
              'cd_nao_conformidade' => 0,
              'cd_processo' => '',
              'cd_nao_conformidade' => '',
              'dt_cadastro' => '',
              'descricao' => '',
              'disposicao' => '',
              'causa' => '',
              'evidencias' => '',
              'acao_corretiva' => '',
              'dt_acao' => '',
              'auditor' => '',
              'verifica_eficacia' => '',
              'data_verif' => '',
              'rnc_aberto' => '',
              'auditor_verif' => '',
              'dt_encerramento' => '',
              'aberto_por' => 0,
              'status' => '',
              'origem' => '',
              'tipo_acao' => '',
              'cd_responsavel' => '',
              'cd_substituto' => '',
              'cd_gerente' => '',
              'dt_implementacao' => '',
              'numero_cad_nc' => '',
              'envolvidos' => '',
              'aberto_por_nome' => '',
              'dt_limite_apres' => '',
              'fl_apresenta_ac' => 'N',
              'fl_ac' => 0,
              'cd_nao_conformidade_origem_evento' => '',
              'ds_nao_conformidade_origem_evento' => '',
              'ds_analise_abrangencia' => ''
            );
        }
        else
        {
            $this->nao_conformidade_model->cadastro($result, $args);
            $data['row'] = $result->row_array();
        }

        $args['cd_usuario']    = $data['row']['cd_responsavel'];
        $args['cd_substituto'] = $data['row']['cd_substituto'];
		
        $this->nao_conformidade_model->comboResponsavel($result, $args);
        $data['ar_responsavel'] = $result->result_array();
		
        $this->nao_conformidade_model->comboOrigemEvento($result, $args);
        $data['ar_origem_evento'] = $result->result_array();		

        $this->load->view('gestao/nc/cadastro.php', $data);
    }

    function cadastroSalvar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_nao_conformidade"]               = $this->input->post("cd_nao_conformidade", TRUE);
        $args["cd_processo"]                       = $this->input->post("cd_processo", TRUE);
        $args["cd_responsavel"]                    = $this->input->post("cd_responsavel", TRUE);
        $args["cd_nao_conformidade_origem_evento"] = $this->input->post("cd_nao_conformidade_origem_evento", TRUE);
        $args["cd_substituto"]                     = $this->input->post("cd_substituto", TRUE);
        $args["descricao"]                         = $this->input->post("descricao", TRUE);
        $args["evidencias"]                        = $this->input->post("evidencias", TRUE);
        $args["disposicao"]                        = $this->input->post("disposicao", TRUE);
        $args["ds_analise_abrangencia"]            = $this->input->post("ds_analise_abrangencia", TRUE);
        $args["causa"]                             = $this->input->post("causa", TRUE);
        $args["cd_usuario"]                        = $this->session->userdata('codigo');

        $cd_nao_conformidade_new = $this->nao_conformidade_model->cadastroSalvar($result, $args);
        redirect("gestao/nc/cadastro/" . $cd_nao_conformidade_new, "refresh");
    }

    function acao_corretiva($cd_nao_conformidade = 0)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $data['cd_nao_conformidade'] = intval($cd_nao_conformidade);
        $args['cd_nao_conformidade'] = intval($cd_nao_conformidade);

        $this->nao_conformidade_model->cadastro($result, $args);
        $data['nc'] = $result->row_array();

        $this->nao_conformidade_model->acaoCorretiva($result, $args);
        $data['row'] = $result->row_array();

        if (count($data['row']) == 0)
        {
            $data['row'] = Array(
              'cd_nao_conformidade' => $cd_nao_conformidade,
              'cd_acao' => 0,
              'dt_limite_apres' => $data['nc']['dt_limite_apres'],
              'fl_limite_apres' => $data['nc']['fl_limite_apres'],
              'dt_apres' => '',
              'dt_prop_imp' => '',
              'dt_efe_imp' => '',
              'dt_prop_verif' => '',
              'dt_efe_verif' => '',
              'ac_proposta' => '',
              'raz_nao_imp' => '',
              'dt_prorrogada' => '',
              'dt_prorrogada_em' => '',
              'cd_usuario_prorrogacao' => '',
              'dt_raz_nao_imp' => '',
              'dt_proposta_prorrogacao' => '',
              'fl_prorroga' => 'S',
              'quinto_dia_util' => '',
			  'dt_prorrogacao_verificacao_eficacia' => ''
            );
        }
        else
        {
            $quinto_dia_util = $this->nao_conformidade_model->data_min_prazo_validacao($result, $args);
            $data['row']['quinto_dia_util'] = $quinto_dia_util['quinto_dia_util'];
        }
        $this->load->view('gestao/nc/acao_corretiva.php', $data);
    }

    function acaoCorretivaSalvar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_acao"]                             = $this->input->post("cd_acao", TRUE);
        $args["cd_nao_conformidade"]                 = $this->input->post("cd_nao_conformidade", TRUE);
        $args["dt_limite_apres"]                     = $this->input->post("dt_limite_apres", TRUE);
        $args["ac_proposta"]                         = $this->input->post("ac_proposta", TRUE);
        $args["dt_prop_imp"]                         = $this->input->post("dt_prop_imp", TRUE);
        $args["dt_prorrogada"]                       = $this->input->post("dt_prorrogada", TRUE);
        $args["raz_nao_imp"]                         = $this->input->post("raz_nao_imp", TRUE);
        $args["dt_efe_verif"]                        = $this->input->post("dt_efe_verif", TRUE);
        $args["dt_efe_imp"]                          = $this->input->post("dt_efe_imp", TRUE);
        $args["dt_proposta_prorrogacao"]             = $this->input->post("dt_proposta_prorrogacao", TRUE);
        $args["dt_prop_verif"]                       = $this->input->post("dt_prop_verif", TRUE);
        $args["dt_prorrogacao_verificacao_eficacia"] = $this->input->post("dt_prorrogacao_verificacao_eficacia", TRUE);
        $args["cd_usuario"]                          = $this->session->userdata('codigo');

        $cd_acao_new = $this->nao_conformidade_model->acaoCorretivaSalvar($result, $args);

        if(intval($args['cd_acao']) == 0)
        {
            $this->load->model('projetos/eventos_email_model');

            $cd_evento = 437;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $result = null;

            $this->nao_conformidade_model->cadastro($result, $args);
            $row = $result->row_array();

            $tags = array('[NC]', '[LINK]');

            $subs = array(
                $row['numero_cad_nc'],
                site_url('gestao/nc/acao_corretiva/'.$args["cd_nao_conformidade"])
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');
            
            $args = array( 
                'de'      => 'Não Conformidade',
                'assunto' => $email['assunto'],
                'para'    => $email['para'], 
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args); 
        }

        redirect("gestao/nc/acao_corretiva/" . $args["cd_nao_conformidade"], "refresh");
    }

    function acompanha($cd_nao_conformidade = 0)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $data['cd_nao_conformidade'] = intval($cd_nao_conformidade);
        $args['cd_nao_conformidade'] = intval($cd_nao_conformidade);

        $this->nao_conformidade_model->cadastro($result, $args);
        $data['nc'] = $result->row_array();

        $this->nao_conformidade_model->acompanha($result, $args);
        $data['ar_acompanha'] = $result->result_array();

        $this->load->view('gestao/nc/acompanha.php', $data);
    }

    function acompanhaSalvar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_acompanhamento"]   = $this->input->post("cd_acompanhamento", TRUE);
        $args["cd_nao_conformidade"] = $this->input->post("cd_nao_conformidade", TRUE);
        $args["situacao"]            = $this->input->post("situacao", TRUE);
        $args["cd_usuario"]          = $this->session->userdata('codigo');

        $cd_acompanhamento_new = $this->nao_conformidade_model->acompanhaSalvar($result, $args);
        redirect("gestao/nc/acompanha/" . $args["cd_nao_conformidade"], "refresh");
    }

    function impressao($cd_nao_conformidade = 0)
    {
        $this->load->plugin('fpdf');

        $args = Array();
        $data = Array();
        $result = null;

        $args['cd_nao_conformidade'] = intval($cd_nao_conformidade);

        $this->nao_conformidade_model->cadastro($result, $args);
        $ar_nao_conformidade = $result->row_array();

        $this->nao_conformidade_model->acaoCorretiva($result, $args);
        $ar_acao_corretiva = $result->row_array();

        $this->nao_conformidade_model->acompanha($result, $args);
        $ar_acompanha = $result->result_array();

        $altura_linha = 5;
        $ob_pdf = new PDF('P', 'mm', 'A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');		
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Não Conformidade - " . $ar_nao_conformidade['numero_cad_nc'];
        $ob_pdf->AddPage();

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetFont('segoeuib', '', 16);
        $ob_pdf->MultiCell(190, $altura_linha, "Não Conformidade");
        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetFont('segoeuil', '', 12);
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Número:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['numero_cad_nc']);	

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Data:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['dt_cadastro']);	
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Dt Limite Apres AC:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['dt_limite_apres']);		

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Data Encerramento:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['dt_encerramento']);	

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Aberto por:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['aberto_por_nome']);	

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Processo:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['ds_processo']);	

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Envolvidos:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['envolvidos']);		
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Responsável:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['ds_responsavel']);		

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Substituto:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['ds_substituto']);			
		
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Origem Evento:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['ds_nao_conformidade_origem_evento']);			
		
	
        ### descricao ###
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib','', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Descrição:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['descricao']);

        ### evidencias ###
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib','', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Evidências Objetivas:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['evidencias']);

        ### disposicao ###
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib','', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Disposição:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['disposicao']);

        ### ds_analise_abrangencia ###
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib','', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Análise de Abrangência:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['ds_analise_abrangencia']);

        ### causa ###
        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('segoeuib','', 12);
        $ob_pdf->MultiCell(190, $altura_linha, "Causa da Não Conformidade:");
        $ob_pdf->SetFont('segoeuil', '', 12);
        $ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['causa']);

        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 6);
        $ob_pdf->SetFont('segoeuib', '', 16);
        $ob_pdf->MultiCell(190, 4.5, "Ação Corretiva");

        $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
        $ob_pdf->SetFont('Courier', '', 12);
        if (count($ar_acao_corretiva) > 0)
        {
			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Data limite para apresentação:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_nao_conformidade['dt_limite_apres']);			
			
			
            ### proposta ac ###
            $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
            $ob_pdf->SetFont('segoeuib','', 12);
            $ob_pdf->MultiCell(190, $altura_linha, "Ação Corretiva Proposta:");
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['ac_proposta']);


			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Data da apresentação:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_apres']);	

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Data proposta:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_prop_imp']);	

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Prorrogada até:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_prorrogada']);	

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Data da efetiva implementação:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_efe_imp']);		

			$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
			$ob_pdf->SetFont('segoeuib','', 12);
			$ob_pdf->MultiCell(190, $altura_linha, "Data da verificação eficácia:");
			$ob_pdf->SetFont('segoeuil', '', 12);
			$ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_prop_verif']);				
			
            ### proposta ac ###
            $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
            $ob_pdf->SetFont('segoeuib','', 12);
            $ob_pdf->MultiCell(190, $altura_linha, "Razão da não implementação até a data proposta:");
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['raz_nao_imp']);

            $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
            $ob_pdf->SetFont('segoeuib','', 12);
            $ob_pdf->MultiCell(190, $altura_linha, "Nova Data Proposta:");
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_proposta_prorrogacao']);
			
            $ob_pdf->SetY($ob_pdf->GetY() + 2.5);
            $ob_pdf->SetFont('segoeuib','', 12);
            $ob_pdf->MultiCell(190, $altura_linha, "Data da efetiva verificação:");
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, $altura_linha, $ar_acao_corretiva['dt_efe_verif']);			
        }
        else
        {
            $ob_pdf->SetFont('segoeuil', '', 12);
            $ob_pdf->MultiCell(190, 4.5, "Ainda não foi apresentada Ação Corretiva.");
        }


        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 6);
        $ob_pdf->SetFont('segoeuib', '', 16);
        $ob_pdf->MultiCell(190, 4.5, "Acompanhamento");
        $ob_pdf->SetY($ob_pdf->GetY() + 4);

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);
        $ob_pdf->SetWidths(array(30, 100, 60));
        $ob_pdf->SetAligns(array('C', 'L', 'L'));
        $ob_pdf->SetFont('segoeuil', '', 11);
        $ob_pdf->Row(array("Data", "Situação", "Usuário"));

        foreach ($ar_acompanha as $ar_reg)
        {
            $ob_pdf->SetFont('segoeuil', '', 11);
			$ob_pdf->Row(array($ar_reg['dt_cadastro'], $ar_reg['situacao'] . "\n\n", $ar_reg['registrado']));
        }

        $ob_pdf->Output();
        exit;
    }

    public function anexo($cd_nao_conformidade)
    {
        $this->load->model('projetos/nao_conformidade_model');

        $args['cd_nao_conformidade'] = intval($cd_nao_conformidade);

        $this->nao_conformidade_model->cadastro($result, $args);
        $data['row'] = $result->row_array();
        

        $data['collection'] = $this->nao_conformidade_model->listar_anexo(intval($data['row']['cd_nao_conformidade']));

        $this->load->view('gestao/nc/anexo', $data);
    }

    public function salvar_anexo()
    {
        $this->load->model('projetos/nao_conformidade_model');

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

        $cd_nao_conformidade = $this->input->post('cd_nao_conformidade', TRUE);
        
        $cd_processo = $this->input->post('cd_processo', TRUE);

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $args = array();        
                
                $args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                $args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                $args['cd_usuario']    = $this->session->userdata('codigo');
                
                $this->nao_conformidade_model->salvar_anexo(intval($cd_nao_conformidade), intval($cd_processo), $args);
                
                $nr_conta++;
            }
        }

        redirect('gestao/nc/anexo/'.intval($cd_nao_conformidade), 'refresh');
    }

    public function excluir_anexo($cd_nao_conformidade, $cd_nao_conformidade_anexo)
    {
        $this->load->model('projetos/nao_conformidade_model');

        $this->nao_conformidade_model->excluir_anexo(intval($cd_nao_conformidade_anexo), $this->session->userdata('codigo'));

        redirect('gestao/nc/anexo/'.intval($cd_nao_conformidade), 'refresh');
    }

}
?>