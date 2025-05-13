<?php
// ----------------------

include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Util.String.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');
include('inc/fpdf153/fpdf.php');

include 'oo/start.php';
using( array(
		'projetos.atendimento_protocolo_tipo'
		, 'projetos.atendimento_protocolo_discriminacao'
    	, 'projetos.mala_direta_integracao' 
) );

// ----------------------

	class eprev_atendimento_protocolo_lista_mala
    {
        private $db;
        private $filtro;
        private $divisao;
        private $usuario_logado;

        function __construct( $_db, $_u )
        {
            $this->db = $_db;
            $this->usuario_logado = $_u;

            $this->filtro = new helper_correspondencia_gap__fetch_by_filter();

            $this->divisao = $_divisao;
            $this->requestParams();
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
        if (isset($_POST["filtro__cd_atendimento_protocolo_tipo__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_tipo( $_POST["filtro__cd_atendimento_protocolo_tipo__select"] );
			}
            if (isset($_POST["filtro__cd_atendimento_protocolo_discriminacao__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_discriminacao( $_POST["filtro__cd_atendimento_protocolo_discriminacao__select"] );
			}
			if (isset($_POST["FiltroEmpresaText"]))
            {
                $this->filtro->setcd_empresa( $_POST["FiltroEmpresaText"] );
			}
            if (isset($_POST["FiltroREText"]))
            {
                $this->filtro->setcd_registro_empregado( $_POST["FiltroREText"] );
			}
            if (isset($_POST["FiltroSeqText"]))
            {
                $this->filtro->setseq_dependencia( $_POST["FiltroSeqText"] );
			}
            if (isset($_POST["FiltroNomeText"]))
            {
                $this->filtro->set_nome( $_POST["FiltroNomeText"] );
			}
            if (isset($_POST["FiltroDataGapText"]))
            {
                $this->filtro->dt_criacao__inicial = $_POST["FiltroDataGapText"];
			}
            if (isset($_POST["FiltroDataGap_final_Text"]))
            {
                $this->filtro->dt_criacao__final= $_POST["FiltroDataGap_final_Text"];
			}
            if (isset($_POST["FiltroHoraGapText"]))
            {
                $this->filtro->hr_criacao__inicial = $_POST["FiltroHoraGapText"];
			}
            if (isset($_POST["FiltroHoraGap_final_Text"]))
            {
                $this->filtro->hr_criacao__final= $_POST["FiltroHoraGap_final_Text"];
			}
        	if (isset($_POST["filtro__cd_usuario_criacao__select"]))
            {
                $this->filtro->setcd_usuario_criacao( $_POST["filtro__cd_usuario_criacao__select"] );
			}
			
            if (isset($_POST["filtro__cd_atendimento__text"]))
            {
                $this->filtro->setcd_atendimento( $_POST["filtro__cd_atendimento__text"] );
			}
            if (isset($_POST["filtro__cd_encaminhamento__text"]))
            {
                $this->filtro->setcd_encaminhamento( $_POST["filtro__cd_encaminhamento__text"] );
			}
        }

        public function loadLista()
        {
            $entity = new entity_projetos_atendimento_protocolo();
            $service = new service_projetos( $this->db );

            $result = $service->correspondenciaGAP_fetchByFilter( $this->filtro );

            $service = null;

            return $result;
        }

        public function integrar_mala_direta()
        {
        	$res = $this->loadLista();

        	$dados = new e_mala_direta_integracao_collection();

        	while( $row = pg_fetch_array($res) )
        	{
        		if($row['cd_empresa']!='' AND $row['cd_registro_empregado']!='')
        		{
		        	$registro = new e_mala_direta_integracao();
		        	$registro->cd_empresa = $row['cd_empresa'];
		        	$registro->cd_registro_empregado = $row['cd_registro_empregado'];

		        	if($row['seq_dependencia']=='')
		        	{
			        	$registro->seq_dependencia = '0';
		        	}
		        	else
		        	{
			        	$registro->seq_dependencia = $row['seq_dependencia'];
		        	}

		        	$registro->usuario = $this->usuario_logado;
		        	$dados->add( $registro );
        		}
        	}
        	
        	$b = mala_direta_integracao::create_new_package($this->usuario_logado, $dados);

        	if( $b ) echo 'true';
        	else echo 'false';
        }
    }

// ----------------------

$esta = new eprev_atendimento_protocolo_lista_mala($db, $U);
$esta->integrar_mala_direta();

?>