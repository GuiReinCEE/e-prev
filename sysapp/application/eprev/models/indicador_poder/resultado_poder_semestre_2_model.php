<?php
class resultado_poder_semestre_2_model extends Model
{
	var $enum_indicador = 0;
	var $nr_semestre    = 2;
	
	function __construct()
	{
		parent::Model();
		$this->enum_indicador = intval(enum_indicador::PODER_RESULTADO_SEMESTRE_2);
	}

	function listar( &$result, $args=array() )
	{
		$sql = "
            SELECT r.cd_indicador,
                   i.ds_indicador,
                   r.nr_ano,
                   r.nr_faixa,
                   r.nr_indice,
				   r.dt_referencia,
				   TO_CHAR(r.dt_referencia,'MM') AS nr_mes,
				   (SELECT pm.nr_peso
					  FROM indicador_poder.parametro_meta pm
					 WHERE pm.cd_indicador = it.cd_indicador
					   AND pm.nr_semestre  = r.nr_semestre
					   AND pm.nr_ano       = r.nr_ano
					 ORDER BY pm.nr_faixa
					 LIMIT 1) AS nr_peso			   
              FROM indicador_poder.resultado r
              JOIN indicador.indicador_tabela it
                ON it.cd_indicador_tabela = r.cd_indicador
			  JOIN indicador.indicador i
                ON i.cd_indicador = it.cd_indicador
              JOIN indicador.indicador_tabela ita
                ON ita.cd_indicador_tabela = ".intval($args['cd_indicador_tabela'])."
              JOIN indicador.indicador_periodo ip
                ON ip.cd_indicador_periodo = ita.cd_indicador_periodo
               AND ip.nr_ano_referencia = r.nr_ano				
             WHERE r.dt_exclusao IS NULL
               AND r.nr_semestre = ".intval($this->nr_semestre)."
             ORDER BY i.nr_ordem, r.nr_ano, r.nr_semestre  ASC
		";
		$result = $this->db->query($sql);
	}

    function criar_indicador()
	{
		$data['label_0'] = "Ano";
		$data['label_1'] = "Mês";
		$data['label_2'] = "Indicador";
        $data['label_3'] = "Faixa";
        $data['label_4'] = "Índice";
        $data['label_5'] = "Peso";
        $data['label_6'] = "Faixa Mínima";

        $this->load->helper(array('indicador'));
		$this->load->model('indicador_poder/parametro_meta_model');
		
		$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

        $tabela = indicador_tabela_aberta($this->enum_indicador);

		#echo "<PRE>".print_r($tabela,true)."</PRE>"; exit;

        if(count($tabela) <= 0)
        {
            return false;
        }
        else
        {
            $indicador = array();
            $linha = 0;
            $vl_indice_total = 0;
		
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
            $this->listar($result, $args);
            $ar_reg = $result->result_array();
			

			foreach($ar_reg as $ar_item)
			{
				$indicador[$linha][0] = $ar_item["nr_ano"];
				$indicador[$linha][1] = $ar_item["nr_mes"];
				$indicador[$linha][2] = $ar_item['ds_indicador'];
				$indicador[$linha][3] = $ar_item['nr_faixa'];
				$indicador[$linha][4] = $ar_item['nr_peso'];
				$indicador[$linha][5] = $ar_item['nr_indice'];
				$indicador[$linha][6] = 1;
				$vl_indice_total += $ar_item['nr_indice'];
				$linha++;
			}
			$linha_fim_grafico = $linha;

            $sql = " 
					DELETE 
					  FROM indicador.indicador_parametro 
					 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 
				   ";				

			### CABEÇALHO ###
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			### VALORES ###
			$linha = 1;
			for($i=0; $i < count($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, $indicador[$i][0], 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, $indicador[$i][1], 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode(substr($indicador[$i][2],0,20)), 'left' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
				$linha++;
			}

			### INFO ADICIONAL ###
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, '', 'center'  );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha,'', 'center');
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha,'', 'center');
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha,'', 'center');
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha,'', 'center');

			$linha++;
			
			$vl_indice_total = $vl_indice_total/100;
			$param['nr_ano'] = $tabela[0]['nr_ano_referencia'];
			$param['vl_indice'] = $vl_indice_total;
			$nr_faixa_final =  $this->parametro_meta_model->getIndiceFaixa($param);
			
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode('<b>Índice Desempenho</b>'), 'center'  );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, '<b>'.app_decimal_para_php($nr_faixa_final).'</b>', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, '<b>'.app_decimal_para_php($vl_indice_total).'</b>', 'center');
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, '', 'center' );

			$linha++;

			$param['nr_ano'] = $tabela[0]['nr_ano_referencia'];
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($this->parametro_meta_model->getIndiceFixo($param)), 'center'  );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, '', 'center');	
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, '', 'center' );			
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, '', 'center' );			

			$linha++;

			$param['nr_ano'] = $tabela[0]['nr_ano_referencia'];
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($this->parametro_meta_model->getIndiceVariavel($param)), 'center'  );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, '', 'center');					
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, '', 'center' );
			$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, '', 'center' );
			
			
			### GRAFICO ###
			$coluna_para_ocultar='6';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;6,6,0,0',
				"2,2,1,$linha_fim_grafico",
				"3,3,1,$linha_fim_grafico;6,6,1,$linha_fim_grafico-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,
				2
			);

			
			
			### GRAVA ###
            $this->db->query($sql);

            return true;
        }
    }
	
}
?>