<?php
class relatorio_acoes_corretivas extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
			$this->load->view('gestao/relatorio_acoes_corretivas/index.php');
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }

    function quadro_resumo()
    {
        CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
			$this->load->model('projetos/relatorio_acoes_corretivas_model');
            $data['conformidade'] = array();
            $data['corretiva '] = array();
            $result = null;

            $this->relatorio_acoes_corretivas_model->quadro_resumo_conformidade( $result );
            $data['conformidade'] = $result->result_array();

            $this->relatorio_acoes_corretivas_model->quadro_resumo_corretiva( $result );
            $data['corretiva'] = $result->result_array();

            $this->load->view('gestao/relatorio_acoes_corretivas/quadro_resumo_result', $data);
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }

    function corretivas_ven()
    {
        CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
            $this->load->library('charts');
			$this->load->model('projetos/relatorio_acoes_corretivas_model');
            $data['collection'] = array();
            $result = null;

            $this->relatorio_acoes_corretivas_model->corretivas_com_prazo_vencido( $result );
            $data['collection'] = $result->result_array();

            $this->relatorio_acoes_corretivas_model->quadro_resumo_corretiva( $result );
            $row= $result->result_array();
            $data['qt_ac_nao_apresentada_fora'] = $row[0]['qt_ac_nao_apresentada_fora'];
            $data['total'] = $row[0]['qt_ac_apresentada_prazo']+$row[0]['qt_ac_apresentada_fora'];

            $this->load->view('gestao/relatorio_acoes_corretivas/corretivas_prazo_vencido_result', $data);
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }

    function corretivas_fora()
    {
        CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
            $this->load->library('charts');
			$this->load->model('projetos/relatorio_acoes_corretivas_model');
            $data['collection'] = array();
            $result = null;

            $this->relatorio_acoes_corretivas_model->corretivas_fora_prazo( $result );
            $data['collection'] = $result->result_array();

            $this->relatorio_acoes_corretivas_model->quadro_resumo_corretiva( $result );
            $row= $result->result_array();
            $data['qt_ac_apresentada_prazo'] = $row[0]['qt_ac_apresentada_prazo'];
            $data['qt_ac_apresentada_fora'] = $row[0]['qt_ac_apresentada_fora'];

            $this->load->view('gestao/relatorio_acoes_corretivas/corretivas_fora_prazo_result', $data);
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }

    function corretivas_implementadas_ven()
    {
        CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
            $this->load->library('charts');
			$this->load->model('projetos/relatorio_acoes_corretivas_model');
            $data['collection'] = array();
            $result = null;

            $this->relatorio_acoes_corretivas_model->corretivas_imple_vencido( $result );
            $data['collection'] = $result->result_array();

            $this->relatorio_acoes_corretivas_model->quadro_resumo_conformidade( $result );
            $row= $result->result_array();
            $data['qt_nao_implementada_fora'] = $row[0]['qt_nao_implementada_fora'];
            $data['total'] = $row[0]['qt_implementada_prazo']+$row[0]['qt_implementada_fora'];

            $this->load->view('gestao/relatorio_acoes_corretivas/corretivas_imple_vencido_result', $data);
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }

    function corretivas_implementadas_fora()
    {
        CheckLogin();

		#if(gerencia_in(array('GAP')))
		#{
            $this->load->library('charts');
			$this->load->model('projetos/relatorio_acoes_corretivas_model');
            $data['collection'] = array();
            $result = null;

            $this->relatorio_acoes_corretivas_model->corretivas_imple_fora_prazo( $result );
            $data['collection'] = $result->result_array();

            $this->relatorio_acoes_corretivas_model->quadro_resumo_conformidade( $result );
            $row= $result->result_array();
            $data['qt_implementada_prazo'] = $row[0]['qt_implementada_prazo'];
            $data['qt_implementada_fora'] = $row[0]['qt_implementada_fora'];

            $this->load->view('gestao/relatorio_acoes_corretivas/corretivas_imple_fora_prazo_result', $data);
		#}
		#else
		#{
		#	exibir_mensagem("ACESSO NÃO PERMITIDO");
		#}
    }
}
?>