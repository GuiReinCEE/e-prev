<?php
class relatorio extends Controller
{
    function __construct()
    {
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/relatorios_model');
    }
	
	function relatorio_dinamico()
    {
		if (gerencia_in(array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->load->view('servico/relatorio/relatorio_dinamico');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
    
	function listar_relatorio_dinamico()
    {
		if (gerencia_in(array('GI')))
        {
			$args = Array();
			$data = Array();
			$result = null;

			$this->relatorios_model->listar_relatorio_dinamico( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('servico/relatorio/relatorio_dinamico_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function index()
    {
		$args = Array();
        $data = Array();
        $result = null;
		
        $this->load->view('servico/relatorio/index');
    }

    function listar()
    {
		$args = Array();
        $data = Array();
        $result = null;

		$args["cd_usuario"]  = $this->session->userdata('codigo');
		$args["cd_gerencia"] = $this->session->userdata('divisao');
		$args["cd_gerencia_ant"] = $this->session->userdata('divisao_ant');

        $this->relatorios_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('servico/relatorio/index_result', $data);
    }
	
	function cadastro($cd_relatorio = 0)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = intval($cd_relatorio);
		
		$this->relatorios_model->esquema_tabela( $result, $args );
		$data['arr_esquema_tabela'] = $result->result_array();
		
		$this->relatorios_model->restricao( $result, $args );
		$data['arr_restricao'] = $result->result_array();
		
		$this->relatorios_model->sistema( $result, $args );
		$data['arr_sistema'] = $result->result_array();
		
		$this->relatorios_model->tipo( $result, $args );
		$data['arr_tipo'] = $result->result_array();
		
		$this->relatorios_model->fonte( $result, $args );
		$data['arr_fonte'] = $result->result_array();
		
		$data['arr_mostrar_checked'] = array();
		
		if($args['cd_relatorio'] == 0)
		{
			$data['row'] = array(
				'cd_relatorio'      => $args['cd_relatorio'],
				'titulo'            => '',
				'query'             => '',
				'clausula_where'    => '',
				'ordem'             => '',
				'grupo'             => '',
				'esquema_tabela'    => '',
				'divisao'           => '',
				'cd_proprietario'   => '',
				'restricao_acesso'  => '',
				'cd_projeto'        => '',
				'especie'           => '',
				'tipo'              => '',
				'tam_fonte_titulo'  => '',
				'fonte'             => '',
				'tam_fonte'         => '',
				'pos_x'             => '',
				'largura'           => '',
				'orientacao'        => '',
				'mostrar_cabecalho' => '',
				'mostrar_linhas'    => ''
			);
		}
		else
		{
			$this->relatorios_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
		
		$this->load->view('servico/relatorio/cadastro', $data);
	}

	function salvar()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$arr_esquema_tabela = explode('.', $this->input->post("esquema_tabela", TRUE));
		
		$args['cd_relatorio']      = $this->input->post("cd_relatorio", TRUE);
		$args['titulo']            = $this->input->post("titulo", TRUE);
		$args['qr_sql']            = $this->input->post("qr_sql", TRUE);
		$args['query']             = $this->input->post("query", TRUE);
		$args['clausula_where']    = $this->input->post("clausula_where", TRUE);
		$args['ordem']             = $this->input->post("ordem", TRUE);
		$args['grupo']             = $this->input->post("grupo", TRUE);
		$args['esquema']           = (isset($arr_esquema_tabela[0]) ? $arr_esquema_tabela[0] : "");
		$args['tabela']            = (isset($arr_esquema_tabela[1]) ? $arr_esquema_tabela[1] : "");
		$args['divisao']           = $this->input->post("cd_proprietario_gerencia", TRUE);
		$args['cd_proprietario']   = $this->input->post("cd_proprietario", TRUE);
		$args['restricao_acesso']  = $this->input->post("restricao_acesso", TRUE);
		$args['cd_projeto']        = $this->input->post("cd_projeto", TRUE);
		$args['especie']           = $this->input->post("especie", TRUE);
		$args['tipo']              = $this->input->post("tipo", TRUE);
		$args['tam_fonte_titulo']  = $this->input->post("tam_fonte_titulo", TRUE);
		$args['fonte']             = $this->input->post("fonte", TRUE);
		$args['tam_fonte']         = $this->input->post("tam_fonte", TRUE);
		$args['pos_x']             = $this->input->post("pos_x", TRUE);
		$args['largura']           = $this->input->post("largura", TRUE);
		$args['orientacao']        = $this->input->post("orientacao", TRUE);
		$args['mostrar_cabecalho'] = $this->input->post("mostrar_cabecalho", TRUE);
		$args['mostrar_linhas']    = $this->input->post("mostrar_linhas", TRUE);
		$args['cd_usuario']        = $this->session->userdata('codigo');
		
		$args['mostrar_sombreamento'] = 'N';
		
		$cd_relatorio = $this->relatorios_model->salvar( $result, $args );
		
		redirect("servico/relatorio/cadastro/".$cd_relatorio, "refresh" );
	}
	
	function excluir($cd_relatorio)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $cd_relatorio;
		$args['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->relatorios_model->excluir( $result, $args );
		
		redirect("servico/relatorio/relatorio_dinamico", "refresh" );
	}
	
	function listar_colunas()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $this->input->post("cd_relatorio", TRUE);
		
		$this->relatorios_model->listar_colunas( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('servico/relatorio/colunas_result', $data);
	}
	
	function salvar_coluna()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $this->input->post("cd_relatorio", TRUE);
		$args['cd_coluna']    = $this->input->post("cd_coluna", TRUE);
		$args['nome_coluna']  = $this->input->post("nome_coluna", TRUE);
		$args['alinhamento']  = $this->input->post("alinhamento", TRUE);
		$args['largura']      = $this->input->post("largura", TRUE);
		
		$this->relatorios_model->salvar_coluna( $result, $args );
	}
	
	function excluir_coluna()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $this->input->post("cd_relatorio", TRUE);
		$args['cd_coluna']    = $this->input->post("cd_coluna", TRUE);
		
		$this->relatorios_model->excluir_coluna( $result, $args );
	}
	
	function gerar_documento($cd_relatorio, $tipo = 'txt')
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $cd_relatorio;
		
		$this->relatorios_model->carrega($result, $args);
		$row = $result->row_array();
		
		/*
		$where = str_replace('{cd_usuario}', $this->session->userdata('codigo'), $row['clausula_where']);	
		$args['query']          = $row['query'];
		$args['esquema_tabela'] = $row['esquema_tabela'];
		$args['where']          = $where;
		$args['grupo']          = $row['grupo'];
		$args['ordem']          = $row['ordem'];
		$this->relatorios_model->execute_sql( $result, $args );
		*/
		
		$args['qr_sql'] = str_replace('{cd_usuario}', $this->session->userdata('codigo'), $row['qr_sql']);
		$this->relatorios_model->qr_execute($result, $args);		
		$arr = $result->result_array();

		$txt = '';
		
		foreach($arr as $item)
		{
			$i = 0;
			foreach($item as $item2)
			{
				if(intval($row['num_colunas']) > $i)
				{						
					if($i == 0)
					{
						$txt .= $item2;
					}
					else
					{
						$txt .= ';'.$item2;
					}
					
					$i ++;
				}
			}		
			$txt .= chr(13).chr(10);
		}

		$arq = './up/meu_relatorio/'.$this->session->userdata('usuario').'.'.$tipo;
		$arq_i = fopen($arq, 'w+');
		fwrite($arq_i, $txt);
		fclose($arq_i);
		
		$plan = fopen($arq, 'r');
		
		header('Content-Type:application/'.$tipo);
		header('Content-Disposition:attachment; filename='.$this->session->userdata('usuario').'.'.$tipo);
		header('Content-Transfer-Encoding:binary');
		fpassthru($plan);
		fclose($plan);
	}
	
	function gerar_pdf($cd_relatorio)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_relatorio'] = $cd_relatorio;
		
		$this->relatorios_model->carrega($result, $args);
		$row = $result->row_array();
		
		$this->relatorios_model->listar_colunas($result, $args);
		$arr_colunas = $result->result_array();
		
		/*
		$where = str_replace('{cd_usuario}', $this->session->userdata('codigo'), $row['clausula_where']);	
		$args['query']          = $row['query'];
		$args['esquema_tabela'] = $row['esquema_tabela'];
		$args['where']          = $where;
		$args['grupo']          = $row['grupo'];
		$args['ordem']          = $row['ordem'];
		$this->relatorios_model->execute_sql( $result, $args );
		*/
		
		$args['qr_sql'] = str_replace('{cd_usuario}', $this->session->userdata('codigo'), $row['qr_sql']);
		$this->relatorios_model->qr_execute($result, $args);		
		$arr = $result->result_array();
		
		$this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
		
		$ob_pdf->SetFont($row['fonte_real'], '', $row['tam_fonte_titulo']);
		
        $ob_pdf->header_titulo_texto = $row['titulo'];
		
		$ob_pdf->AddPage();
		
		$ob_pdf->SetFont($row['fonte_real'], '', $row['tam_fonte']);
		
		$arr_tamanho      = array();
		$arr_alinhamento  = array();
		$arr_coluna       = array();
		$ar_fl_somar      = array();

		$ob_pdf->setX($row['pos_x']);
		
		foreach($arr_colunas as $item)
		{
			$arr_largura[]       = $item['largura'];
			$arr_alinhamento[]   = $item['align'];
			$arr_alinhamento_c[] = 'C';
			$arr_coluna[]        = $item['nome_coluna'];
			$ar_fl_somar[]       = $item['fl_somar'];
		}
		
		#echo print_r($ar_fl_somar); exit;
		
		if((trim($row['mostrar_cabecalho']) == 'S') OR (trim($row['mostrar_linhas']) == 'S'))
		{
			$ob_pdf->SetWidths($arr_largura);
		}
		
		if(trim($row['mostrar_cabecalho']) == 'S')
		{
			$ob_pdf->SetAligns($arr_alinhamento_c);
			$ob_pdf->Row($arr_coluna);
		}
		
		if(trim($row['mostrar_linhas']) == 'S')
		{
			$ob_pdf->SetAligns($arr_alinhamento);
			$ar_soma = array();
			
			$x = 0;
			foreach($arr as $item)
			{
				$i = 0;
				$arr_valor = array();

				foreach($item as $item2)
				{
					if(intval($row['num_colunas']) > $i)
					{
						$arr_valor[] = $item2;
						$ar_soma[$x][$i] = $item2;
					}
					$i++;
				}
				$ob_pdf->setX($row['pos_x']);
				$ob_pdf->Row($arr_valor);
				
				$x++;
			}
			
			#TOTALIZADOR
			$ar_soma_row = array();
			$fl_totaliza = false;
			foreach($ar_soma as $array)
			{
				foreach(array_keys($array) as $key)
				{
					if($ar_fl_somar[$key] == "S")
					{
						$fl_totaliza = true;
						
						if(isset($ar_soma_row[$key]))
						{
							$ar_soma_row[$key] += $array[$key];
						}
						else
						{
							$ar_soma_row[$key] = $array[$key];
						}
					}
					else
					{
						$ar_soma_row[$key] = "";
					}
				}
			}
			
			#TOTALIZADOR
			if($fl_totaliza)
			{
				$ob_pdf->setX($row['pos_x']);
				$ob_pdf->Row($ar_soma_row);			
			}
		}

		$ob_pdf->Output();
	}
}
?>