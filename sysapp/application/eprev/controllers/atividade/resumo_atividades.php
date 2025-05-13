<?php
class resumo_atividades extends Controller
{
    function __construct()
    {
        parent::Controller();
		$this->load->model('projetos/resumo_atividades_model');
    }

    function index()
    {
		CheckLogin();
        if(gerencia_in(array('GI')))
        {
			$result = null;
			$args = Array();
			$data = Array();
			
            $this->resumo_atividades_model->comboAtendente($result, $args);
            $data["ar_atendente"] = $result->result_array();	
            
			$this->load->view('atividade/resumo_atividades/index.php',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        CheckLogin();
        if(gerencia_in(array('GI')))
        {
            $data['collection'] = array();
            $result = null;
            $args = array();

            $args['ano']        = $this->input->post('ano', TRUE);
            $args['mes']        = '';
            $args['cd_usuario'] = $this->input->post('cd_usuario', TRUE);
            //SUPORTE
            //ANO ANTERIOR DO FILTRO
            //Abertas
            $this->resumo_atividades_model->anteriorAbertaSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_aberta_sup'] = $row['qt_ant_aberta'];
            //Crítica Auto (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritAutoSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_auto_sup'] = $row['qt_ant_concluida'];
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritUserSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_user_sup'] = $row['qt_ant_concluida'];
            //Não Crítica (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritNaoSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_nao_sup'] = $row['qt_ant_concluida'];
            //Total
            $this->resumo_atividades_model->anteriorConcluidaSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_sup'] = $row['qt_ant_concluida'];
            //Canceladas
            $this->resumo_atividades_model->anteriorCanceladasSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_cancelada_sup'] = $row['qt_ant_cancelada'];
            //Suspensas
            $this->resumo_atividades_model->anteriorSuspensasSuporte( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_suspensa_sup'] = $row['qt_ant_suspensa'];

            //ANO ATUAL DO FILTRO
            //Abertas
            $this->resumo_atividades_model->abertasSuporte( $result, $args );
            $data['abertas_sup'] = $this->montaMeses($result->result_array());
             //Crítica Auto (Conc)
            $this->resumo_atividades_model->concluidaCritAutoSuporte( $result, $args );
            $data['concluida_crit_auto_sup'] = $this->montaMeses($result->result_array());
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->concluidaCritUserSuporte( $result, $args );
            $data['concluida_crit_user_sup'] = $this->montaMeses($result->result_array());
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->concluidaCritNaoSuporte( $result, $args );
            $data['concluida_crit_nao_sup'] = $this->montaMeses($result->result_array());
            //Total
            $this->resumo_atividades_model->concluidasSuporte( $result, $args );
            $data['concluidas_sup'] = $this->montaMeses($result->result_array());
            //Canceladas
            $this->resumo_atividades_model->canceladasSuporte( $result, $args );
            $data['canceladas_sup'] = $this->montaMeses($result->result_array());
            //Suspensas
            $this->resumo_atividades_model->suspensasSuporte( $result, $args );
            $data['suspensas_sup'] = $this->montaMeses($result->result_array());

            //SUPORTE
            //ANO ANTERIOR DO FILTRO
            //Abertas
            $this->resumo_atividades_model->anteriorAbertasSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_aberta_sis'] = $row['qt_ant_aberta'];
            //Crítica Auto (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritAutoSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_auto_sis'] = $row['qt_ant_concluida'];
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritUserSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_user_sis'] = $row['qt_ant_concluida'];
            //Não Crítica (Conc)
            $this->resumo_atividades_model->anteriorConcluidaCritNaoSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_crit_nao_sis'] = $row['qt_ant_concluida'];
            //Total
            $this->resumo_atividades_model->anteriorConcluidaSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_concluida_sis'] = $row['qt_ant_concluida'];
            //Canceladas
            $this->resumo_atividades_model->anteriorCanceladasSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_cancelada_sis'] = $row['qt_ant_cancelada'];
            //Suspensas
            $this->resumo_atividades_model->anteriorSuspensasSistema( $result, $args );
            $row = $result->row_array();
            $data['qt_anterior_suspensa_sis'] = $row['qt_ant_suspensa'];

            //ANO ATUAL DO FILTRO
            //Abertas
            $this->resumo_atividades_model->abertasSistema( $result, $args );
            $data['abertas_sis'] = $this->montaMeses($result->result_array());
            //Crítica Auto (Conc)
            $this->resumo_atividades_model->concluidaCritAutoSistema( $result, $args );
            $data['concluida_crit_auto_sis'] = $this->montaMeses($result->result_array());
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->concluidaCritUserSistema( $result, $args );
            $data['concluida_crit_user_sis'] = $this->montaMeses($result->result_array());
            //Crítica Usuário (Conc)
            $this->resumo_atividades_model->concluidaCritNaoSistema( $result, $args );
            $data['concluida_crit_nao_sis'] = $this->montaMeses($result->result_array());
            //Total
            $this->resumo_atividades_model->concluidasSistema( $result, $args );
            $data['concluidas_sis'] = $this->montaMeses($result->result_array());
            //Canceladas
            $this->resumo_atividades_model->canceladasSistema( $result, $args );
            $data['canceladas_sis'] = $this->montaMeses($result->result_array());
            //Suspensas
            $this->resumo_atividades_model->suspensasSistema( $result, $args );
            $data['suspensas_sis'] = $this->montaMeses($result->result_array());

            //Resumo Acumulado por Divisão
            #$this->resumo_atividades_model->resumoDivisao( $result, $args );
            #$data['resumo_divisao'] = $result->result_array();
                   
            $data['ano'] = $args['ano'];

            $this->load->view('atividade/resumo_atividades/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function montaMeses($ar_monta = Array())
    {
        $ar_retorno = Array();

        for($i=1; $i<=12; $i++)
        {
            $ar_retorno[$i] = 0;
        }
        foreach($ar_monta as $item)
        {
            $ar_retorno[$item['nr_mes']] = $item['qt_atividade'];
        }

        return $ar_retorno;

    }
	
    function resumo_gerencia()
    {
		CheckLogin();
        if(gerencia_in(array('GI')))
        {
			$result = null;
			$args = Array();
			$data = Array();
			
            $this->resumo_atividades_model->comboAtendente($result, $args);
            $data["ar_atendente"] = $result->result_array();					
            
			$this->load->view('atividade/resumo_atividades/resumo_gerencia.php',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
	
    function resumo_gerencia_listar()
    {
		CheckLogin();
        if(gerencia_in(array('GI')))
        {	
			$result = null;
			$args = Array();
			$data = Array();
			
			$args['nr_ano']           = $this->input->post("nr_ano", TRUE);
			$args['fl_considerar_gi'] = $this->input->post("fl_considerar_gi", TRUE);
			$args['cd_atendente']     = $this->input->post("cd_atendente", TRUE);
			
            $this->resumo_atividades_model->resumoGerencia($result, $args);
            $data["ar_reg"] = $result->result_array();			
			
            $this->load->view('atividade/resumo_atividades/resumo_gerencia_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}
?>