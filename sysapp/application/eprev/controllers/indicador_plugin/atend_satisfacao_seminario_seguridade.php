<?php
class atend_satisfacao_seminario_seguridade extends Controller
{
	var	$label_0 = "Ano";
	var	$label_1 = "Nº de Participantes";
	var	$label_2 = "Total de Satisfeitas";
	var	$label_3 = "Total de Avaliações";
    var	$label_4 = "% Satisfação";
    var $label_5 = "Observação";
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_SEMINARIO_GAUCHO_DE_SEGURIDADE);
		
		$this->load->helper( array('indicador') );
		
		$this->load->model( 'indicador_plugin/atend_satisfacao_seminario_seguridade_model' );
    }
	
    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GP' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			#### FECHA PERIODO ENCERRADO PARA ABRIR NOVO ####
			$ar_periodo = indicador_periodo_aberto();
			$ar_tabela  = indicador_tabela_aberta(intval($this->enum_indicador));
			if(intval($ar_periodo[0]["cd_indicador_periodo"]) != intval($ar_tabela[0]["cd_indicador_periodo"]))
			{
				$qr_sql = indicador_db::fechar_periodo_para_indicador(intval($ar_tabela[0]["cd_indicador_tabela"]), $this->session->userdata('codigo'));
				$this->db->query($qr_sql);
			}			
			
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_plugin/atend_satisfacao_seminario_seguridade/index',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GP' ))
        {
	        $args = Array();
			$data = Array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(sizeof($data['tabela'])>0)
			{
				$args['cd_indicador_tabela'] = $data['tabela'][0]['cd_indicador_tabela'];

				$this->atend_satisfacao_seminario_seguridade_model->listar( $result, $args );
				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atend_satisfacao_seminario_seguridade/partial_result', $data);
			}
			else
			{
				exibir_mensagem("Nenhum período aberto para o indicador.");
			}
        }
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function cadastro($cd_atend_satisfacao_seminario_seguridade = 0)
	{		
		if(indicador_db::verificar_permissao(usuario_id(),'GP' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			$args['cd_atend_satisfacao_seminario_seguridade'] = $cd_atend_satisfacao_seminario_seguridade;
			
			if(intval($args['cd_atend_satisfacao_seminario_seguridade']) == 0)
			{				
				$data['row'] = array(
					'cd_atend_satisfacao_seminario_seguridade' => 0,
					'ano_referencia'                           => date('Y'),
					'observacao'                               => '',
					'nr_participante'                          => '',
					'nr_satisfeito'                            => '',
					'nr_avaliacao'                             => '',
					'nr_satisfacao_percentual'                 => ''		           
				);
			}
			else
			{
				$this->atend_satisfacao_seminario_seguridade_model->carregar( $result, $args );
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/atend_satisfacao_seminario_seguridade/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GP' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_atend_satisfacao_seminario_seguridade'] = $this->input->post('cd_atend_satisfacao_seminario_seguridade', true);
			$args["cd_indicador_tabela"]                      = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                            = '01/01/'.$this->input->post("ano_referencia", true);
			$args["nr_participante"]                          = $this->input->post("nr_participante", true);
			$args["nr_satisfeito"]                            = $this->input->post("nr_satisfeito", true);
			$args["nr_avaliacao"]                             = $this->input->post("nr_avaliacao", true);
			$args["observacao"]                               = $this->input->post("observacao", true);
			$args["cd_usuario"]                               = $this->session->userdata('codigo');
			
			$this->atend_satisfacao_seminario_seguridade_model->salvar( $result, $args );
			
			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/atend_satisfacao_seminario_seguridade", "refresh" );
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function excluir($cd_atend_satisfacao_seminario_seguridade)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GP' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args['cd_atend_satisfacao_seminario_seguridade'] = $cd_atend_satisfacao_seminario_seguridade;
			$args["cd_usuario"]                               = $this->session->userdata('codigo');
		
			$this->atend_satisfacao_seminario_seguridade_model->excluir( $result, $args );
			
			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_plugin/atend_satisfacao_seminario_seguridade', 'refresh' );
			}			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GP' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			if(sizeof($tabela) <= 0)
			{
				return false;
			}
			else
			{
				$sql = "DELETE 
				          FROM indicador.indicador_parametro 
						 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela']).";";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
                
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				
				$this->atend_satisfacao_seminario_seguridade_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador = array();
				$linha = 0;

				$contador = sizeof($collection);
				
				foreach( $collection as $item )
				{
					$indicador[$linha][0] = $item['ano_referencia'];
					$indicador[$linha][1] = app_decimal_para_php($item['nr_participante']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_satisfeito']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_avaliacao']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_satisfacao_percentual']);
					$indicador[$linha][5] = $item['observacao'];

					$linha++;
				}

				$linha_sem_media = $linha;

				$indicador = array_reverse($indicador);

				$linha = 1;
				
				for($i = 0; $i < sizeof($indicador); $i++)
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');
					
					$linha++;
				}

				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'4,4,0,0',
					"0,0,1,$linha_sem_media",
					"4,4,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar,
                    1,
					-1,
					-1,
					"S"				
				);

				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
			}
		}
		else
		{
			return false;
		}
	}
}
?>