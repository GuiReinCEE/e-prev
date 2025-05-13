<?php
class acao_preventiva extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
		
        $this->load->model('projetos/acao_preventiva_model');
    }
    
    function index()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $this->acao_preventiva_model->combo_processo($result, $args);
        $data['processo_dd'] = $result->result_array();
		
        $this->acao_preventiva_model->auditores($result, $args);
        $data['arr_auditores'] = $result->result_array();
        
        $this->load->view('gestao/acao_preventiva/index',$data);
    }

    function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["processo"]               = $this->input->post("processo", TRUE);
        $args["usuario"]                = $this->input->post("usuario", TRUE);
        $args["cd_usuario_titular"]     = $this->input->post("cd_usuario_titular", TRUE);
        $args["cd_usuario_substituto"]  = $this->input->post("cd_usuario_substituto", TRUE);
        $args["gerencia"]               = $this->input->post("usuario_gerencia", TRUE);
        $args["dt_inclussao_ini"]       = $this->input->post("dt_inclussao_ini", TRUE);
        $args["dt_inclussao_fim"]       = $this->input->post("dt_inclussao_fim", TRUE);
        $args["dt_proposta_ini"]        = $this->input->post("dt_proposta_ini", TRUE);
        $args["dt_proposta_fim"]        = $this->input->post("dt_proposta_fim", TRUE);
        $args["dt_implementacao_ini"]   = $this->input->post("dt_implementacao_ini", TRUE);
        $args["dt_implementacao_fim"]   = $this->input->post("dt_implementacao_fim", TRUE);
        $args["dt_prorrogacao_ini"]     = $this->input->post("dt_prorrogacao_ini", TRUE);
        $args["dt_prorrogacao_fim"]     = $this->input->post("dt_prorrogacao_fim", TRUE);
        $args["dt_validacao_ini"]       = $this->input->post("dt_validacao_ini", TRUE);
        $args["dt_validacao_fim"]       = $this->input->post("dt_validacao_fim", TRUE);
        $args['cancelamento']           = $this->input->post("cancelamento", TRUE);
        $args['validado']               = $this->input->post("validado", TRUE);
        $args['implementado']           = $this->input->post("implementado", TRUE);
        $args["dt_prazo_validacao_ini"] = $this->input->post("dt_prazo_validacao_ini", TRUE);
        $args["dt_prazo_validacao_fim"] = $this->input->post("dt_prazo_validacao_fim", TRUE);

        manter_filtros($args);
        
        $this->acao_preventiva_model->listar( $result, $args );

        $data['collection'] = $result->result_array();

        $this->load->view('gestao/acao_preventiva/partial_result', $data);
    }

    function cadastro($nr_ano=0, $nr_ap=0)
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $data['nr_ano'] = intval($nr_ano);
        $data['nr_ap'] = intval($nr_ap);

        $this->acao_preventiva_model->combo_processo($result, $args);
        $data['processo_dd'] = $result->result_array();

        if($data['nr_ano'] == 0 AND $data['nr_ap'] ==0 )
        {
            $data['row'] = Array(
				'nr_ano'             => 0,
				'nr_ap'              => 0,
				'numero_cad_ap'      => '',
				'cd_processo'        => '',
				'cd_acao_preventiva' => 0,
				'dt_inclusao'        => '',
				'potencial_nc'       => '',
				'causa_nc'           => '',
				'fonte_info'         => '',
				'acao_proposta'      => '',
				'dt_proposta'        => '',
				'dt_implementacao'   => '',
				'dt_prorrogacao'     => '',
				'dt_validacao'       => '',
				'validado'           => '',
				'dt_cancelado'       => '',
				'usuario_cancelado'  => '',
				'cd_responsavel'     => '',
				'dt_prazo_validacao' => '',
				'quinto_dia_util'    => '',
				'cd_substituto'      => '',
				'dt_prazo_validacao_prorroga' => ''
			);
        }
        else
        {
            $quinto_dia_util = $this->acao_preventiva_model->data_min_prazo_validacao($result, $args);
			
            $args['nr_ano'] = intval($nr_ano);
            $args['nr_ap'] = intval($nr_ap);
			
            $this->acao_preventiva_model->carrega($result, $args);
            $data['row'] = $result->row_array();
			
            $data['row']['quinto_dia_util'] = $quinto_dia_util['quinto_dia_util'];
        }

        $this->load->view('gestao/acao_preventiva/cadastro', $data);
    }

    function salvar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_acao_preventiva"] = $this->input->post("cd_acao_preventiva", TRUE);
        $args["processo"]           = $this->input->post("processo", TRUE);
        $args["potencial_nc"]       = $this->input->post("potencial_nc", TRUE);
        $args["causa_nc"]           = $this->input->post("causa_nc", TRUE);
        $args["fonte_info"]         = $this->input->post("fonte_info", TRUE);
        $args["acao_proposta"]      = $this->input->post("acao_proposta", TRUE);
        $args["dt_proposta"]        = $this->input->post("dt_proposta", TRUE);
        $args["usuario"]            = $this->input->post("usuario", TRUE);
        $args["cd_substituto"]      = $this->input->post("cd_substituto", TRUE);
        $args["gerencia"]           = $this->input->post("usuario_gerencia", TRUE);
        $args["usuario_inc"]        = $this->session->userdata('codigo');
        $args["numero_cad_ap"]      = $this->input->post("numero_cad_ap", TRUE);
        $args["dt_implementacao"]   = $this->input->post("dt_implementacao", TRUE);
        $args["dt_prazo_validacao"] = $this->input->post("dt_prazo_validacao", TRUE);

        $numero_cad_ap = $this->acao_preventiva_model->salvar($result, $args);
        redirect("gestao/acao_preventiva/cadastro/".$numero_cad_ap, "refresh");
    }
	
	function prorrogar_validacao()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args["cd_acao_preventiva"]          = $this->input->post("cd_acao_preventiva", TRUE);
		$args["dt_prazo_validacao_prorroga"] = $this->input->post("dt_prazo_validacao_prorroga", TRUE);
		$args["cd_usuario"]                  = $this->session->userdata('codigo');
		
		$this->acao_preventiva_model->prorrogar_validacao($result, $args);
	}

    function validar($cd_acao_preventiva, $nr_ano, $nr_ap)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_acao_preventiva"] = $cd_acao_preventiva;
        $args["numero_cad_ap"]      = $nr_ano.'/'.$nr_ap;
        
        $args["usuario"]            = $this->session->userdata('codigo');

        $numero_cad_ap = $this->acao_preventiva_model->validar($result, $args);
        redirect("gestao/acao_preventiva/cadastro/".$numero_cad_ap, "refresh");
    }

    function cancelar($cd_acao_preventiva, $nr_ano, $nr_ap)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_acao_preventiva"] = $cd_acao_preventiva;
        $args["numero_cad_ap"]      = $nr_ano.'/'.$nr_ap;

        $args["usuario"]            = $this->session->userdata('codigo');

        $numero_cad_ap = $this->acao_preventiva_model->cancelar($result, $args);
        redirect("gestao/acao_preventiva/cadastro/".$numero_cad_ap, "refresh");
    }

    function acompanhamento($nr_ano, $nr_ap)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $data['nr_ano'] = intval($nr_ano);
        $data['nr_ap'] = intval($nr_ap);
        $data['cd_preventiva_acompanhamento'] = 0;
        
        $args['nr_ano'] = intval($nr_ano);
        $args['nr_ap'] = intval($nr_ap);

        $this->acao_preventiva_model->acompanhamento( $result, $args );
        $data['ar_acompanha'] = $result->result_array();

        $this->load->view('gestao/acao_preventiva/acompanhamento', $data);
    }

    function salvar_acompanhamento()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $numero_cad_ap  = $this->input->post("numero_cad_ap", TRUE);
        $args["nr_ano"]  = $this->input->post("nr_ano", TRUE);
        $args["nr_ap"]  = $this->input->post("nr_ap", TRUE);
        $args["acompanhamento"] = $this->input->post("acompanhamento", TRUE);
        $args["usuario"]        = $this->session->userdata('codigo');

        $this->acao_preventiva_model->salvar_acompanhamento($result, $args);
		redirect("gestao/acao_preventiva/acompanhamento/".$numero_cad_ap, "refresh");
    }

    function prorrogacao($nr_ano, $nr_ap)
    {
        $args = Array();
        $data = Array();
        $result = null;

        $data['nr_ano'] = intval($nr_ano);
        $data['nr_ap'] = intval($nr_ap);
        $data['cd_preventiva_acompanhamento'] = 0;

        $args['nr_ano'] = intval($nr_ano);
        $args['nr_ap'] = intval($nr_ap);

        $this->acao_preventiva_model->implementacao($result, $args);
        $data['implementacao'] = $result->row_array();

        $this->acao_preventiva_model->cancelamento($result, $args);
        $data['cancelamento'] = $result->row_array();

        $this->acao_preventiva_model->prorrogacao( $result, $args );
        $data['ar_prorrogacao'] = $result->result_array();

        $this->load->view('gestao/acao_preventiva/prorrogacao', $data);
    }

    function salvar_prorrogacao()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $numero_cad_ap  = $this->input->post("numero_cad_ap", TRUE);
        $args["nr_ano"]  = $this->input->post("nr_ano", TRUE);
        $args["nr_ap"]  = $this->input->post("nr_ap", TRUE);
        $args["dt_prorrogacao"]  = $this->input->post("dt_prorrogacao", TRUE);
        $args["motivo"] = $this->input->post("motivo", TRUE);
        $args["usuario"]        = $this->session->userdata('codigo');

        $this->acao_preventiva_model->salvar_prorrogacao($result, $args);
        redirect("gestao/acao_preventiva/prorrogacao/".$numero_cad_ap, "refresh");
    }

    function gerar_pdf()
    {
        $this->load->plugin('fpdf');

        $args = Array();
        $data = Array();
        $result = null;

        $numero_cad_ap  = $this->input->post("numero_cad_ap", TRUE);
        $args["nr_ano"]  = $this->input->post("nr_ano", TRUE);
        $args["nr_ap"]  = $this->input->post("nr_ap", TRUE);

        $this->acao_preventiva_model->gerar_pdf($result, $args);
        $row = $result->row_array();

        $this->acao_preventiva_model->acompanhamento( $result, $args );
        $ar_acompanha = $result->result_array();

        $this->acao_preventiva_model->prorrogacao( $result, $args );
        $ar_prorrogacao = $result->result_array();
        
        $ob_pdf = new PDF();

        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10,14,5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Solicitação de Ação Preventiva";

        $ob_pdf->AddPage();
        $ob_pdf->SetY($ob_pdf->GetY() + 2);
        $ob_pdf->SetFont( 'Courier', '', 10 );
        $ob_pdf->MultiCell(190, 4.5, "Nº da SAP: ". $row['numero_cad_ap']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Cadastro: ". $row['dt_inclusao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Proposta: ". $row['dt_proposta']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Prorrogação: ". $row['dt_prorrogacao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Implementação: ". $row['dt_implementacao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Validação Eficácia: ". $row['dt_prazo_validacao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Dt Validação da Eficácia: ". $row['dt_validacao']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Responsável: ". $row['responsavel']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
		$ob_pdf->MultiCell(190, 4.5, "Substituto: ". $row['nome_substituto']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Processo: ". $row['processo']);
        $ob_pdf->SetY($ob_pdf->GetY() + 1);
        $ob_pdf->MultiCell(190, 4.5, "Área Solicitante: ". $row['divisao']);

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->MultiCell(190, 4.5, "Potencial Não conformidade (Risco):");
        $ob_pdf->SetY($ob_pdf->GetY());
        $ob_pdf->MultiCell(190, 4.5, $row['potencial_nc']);

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->MultiCell(190, 4.5, "Causas da Potencial Não-Conformidade:");
        $ob_pdf->SetY($ob_pdf->GetY());
        $ob_pdf->MultiCell(190, 4.5, $row['causa_nc']);

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->MultiCell(190, 4.5, "Fonte de Informação:");
        $ob_pdf->SetY($ob_pdf->GetY());
        $ob_pdf->MultiCell(190, 4.5, $row['fonte_info']);

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->MultiCell(190, 4.5, "Ação Preventiva Proposta:");
        $ob_pdf->SetY($ob_pdf->GetY());
        $ob_pdf->MultiCell(190, 4.5, $row['acao_proposta']);

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0,0,0);
        $ob_pdf->SetWidths(array(192));
        $ob_pdf->SetAligns(array('C'));
        $ob_pdf->SetFont('Courier','B',10);
        $ob_pdf->Row(array("Acompanhamento"));
        $ob_pdf->SetWidths(array(25,103,64));
        $ob_pdf->SetAligns(array('C', 'C', 'C'));
        $ob_pdf->SetFont('Courier','',10);
        $ob_pdf->Row(array("Data", "Situação", "Usuário"));
        $ob_pdf->SetAligns(array('C', 'L', 'L'));
        
        foreach($ar_acompanha as $item)
        {
             $ob_pdf->Row(array($item['dt_inclusao'], $item['acompanhamento'], $item['usuario']));
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 4);
        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0,0,0);
        $ob_pdf->SetWidths(array(192));
        $ob_pdf->SetAligns(array('C'));
        $ob_pdf->SetFont('Courier','B',10);
        $ob_pdf->Row(array("Prorrogação"));
        $ob_pdf->SetWidths(array(25,167));
        $ob_pdf->SetAligns(array('C', 'C'));
        $ob_pdf->SetFont('Courier','',10);
        $ob_pdf->Row(array("Data", "Motivo"));
        $ob_pdf->SetAligns(array('C', 'L'));

        foreach($ar_prorrogacao as $item)
        {
             $ob_pdf->Row(array($item['dt_prorrogacao'], $item['motivo']));
        }

        $ob_pdf->Output();
        exit;
    }

    public function anexo($nr_ano, $nr_ap)
    {
        $this->load->model('projetos/acao_preventiva_model');

        $args = array(
            'nr_ano' => intval($nr_ano),
            'nr_ap'  => intval($nr_ap)
        );

        $this->acao_preventiva_model->carrega($result, $args);
        $data['row'] = $result->row_array();

        $data['collection'] = $this->acao_preventiva_model->listar_anexo(intval($data['row']['cd_acao_preventiva']));

        $this->load->view('gestao/acao_preventiva/anexo', $data);
    }

    public function salvar_anexo()
    {
        $this->load->model('projetos/acao_preventiva_model');

        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

        $nr_ano             = $this->input->post('nr_ano', TRUE);
        $nr_ap              = $this->input->post('nr_ap', TRUE);
        $cd_acao_preventiva = $this->input->post('cd_acao_preventiva', TRUE);

        if($qt_arquivo > 0)
        {
            $nr_conta = 0;

            while($nr_conta < $qt_arquivo)
            {
                $args = array();        
                
                $args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                $args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                $args['cd_usuario']    = $this->session->userdata('codigo');
                
                $this->acao_preventiva_model->salvar_anexo(intval($cd_acao_preventiva), $args);
                
                $nr_conta++;
            }
        }

        redirect('gestao/acao_preventiva/anexo/'.intval($nr_ano).'/'.intval($nr_ap), 'refresh');
    }

    public function excluir_anexo($nr_ano, $nr_ap, $cd_acao_preventiva_anexo)
    {
        $this->load->model('projetos/acao_preventiva_model');

        $this->acao_preventiva_model->excluir_anexo(intval($cd_acao_preventiva_anexo), $this->session->userdata('codigo'));

        redirect('gestao/acao_preventiva/anexo/'.intval($nr_ano).'/'.intval($nr_ap), 'refresh');
    }
}
?>