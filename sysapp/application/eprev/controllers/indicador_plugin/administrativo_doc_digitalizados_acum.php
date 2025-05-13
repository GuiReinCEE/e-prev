<?php
class administrativo_doc_digitalizados_acum extends Controller
{
    var	$label_0 = "Mкs";
	var	$label_1 = "Total";
    var $label_2 = "Observaзгo";
	var $enum_indicador = 0;

    var $fl_permissao = false;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RH_DOCUMENTOS_DIGITALIZADOS_ACUM);

        $this->load->helper( array('indicador') );
        CheckLogin();

        if(gerencia_in(array('GGS' )))
        {
            $this->fl_permissao = true;
        }
        else
        {
            $this->fl_permissao = false;
        }
    }

    function index()
    {
		if($this->fl_permissao)
		{
			// VERIFICA SE EXISTE TABELA NO PERНODO ABERTO, SE NГO EXISTIR, CRIAR TABELA NO PERНODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->load->model('indicador_poder/administrativo_doc_digitalizados_acum_model');
                $this->administrativo_doc_digitalizados_acum_model->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_plugin/administrativo_doc_digitalizados_acum/index.php',$data);
		}
    }

    function listar()
    {
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;

		if($this->fl_permissao)
		{
	        $this->load->model( 'indicador_plugin/administrativo_doc_digitalizados_acum_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador ) );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->administrativo_doc_digitalizados_acum_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/administrativo_doc_digitalizados_acum/partial_result', $data);
			}
			else
			{
				echo "Nenhum perнodo aberto para o indicador.";
			}
        }
    }
}
?>