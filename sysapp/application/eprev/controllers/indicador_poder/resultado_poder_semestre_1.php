<?php
class resultado_poder_semestre_1 extends Controller
{
	var	$label_0 = "Ano";
	var	$label_1 = "Indicador";
	var	$label_2 = "Faixa";
	var	$label_3 = "Indice";

	var $enum_indicador = 0;
	var $fl_permissao = FALSE;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::PODER_RESULTADO_SEMESTRE_1);
		
		if((gerencia_in(array('GGS'))) and ($this->session->userdata('tipo') == "G")) # Gerente da GGS
		{
			$this->fl_permissao = TRUE;
		}
		elseif (usuario_id() == 170)
		{
			$this->fl_permissao = TRUE;
		}		
		elseif (usuario_id() == 103)
		{
			$this->fl_permissao = TRUE;
		}
		elseif (usuario_id() == 251)
		{
			$this->fl_permissao = TRUE;
		}
    }

    function index()
    {
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
                $this->load->model('indicador_poder/resultado_poder_semestre_1_model');
                $this->resultado_poder_semestre_1_model->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_poder/resultado_poder_semestre_1/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;
		
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
        {
	        $this->load->model( 'indicador_poder/resultado_poder_semestre_1_model' );

	        $data['collection'] = array();
	        $result = null;
            $args = array();
			
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->resultado_poder_semestre_1_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_poder/resultado_poder_semestre_1/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }
	
	function fechar_periodo()
	{
       
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_poder/realizacao_orcam_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->realizacao_orcam_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{
				echo "Não foi identificado período aberto para o Indicador";
			}
			else
			{
				$sql=" 
                    UPDATE indicador.indicador_tabela 
                       SET dt_fechamento_periodo         = CURRENT_TIMESTAMP,
                           cd_usuario_fechamento_periodo = ".intval(usuario_id())." 
                     WHERE cd_indicador_tabela=". intval($tabela[0]['cd_indicador_tabela']);

				if(trim($sql)!=''){$this->db->query($sql);}

			}
			redirect( 'indicador_poder/resultado_poder_semestre_1' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
		
	}	
}
?>