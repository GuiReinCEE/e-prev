<?php
class os70242 extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	public function perguntas()
	{
		$qr_sql = "
			SELECT a.nome,
                   p.cd_pergunta, 
                   p.texto 
              FROM projetos.enquete_perguntas p
              JOIN projetos.enquete_agrupamentos a
                ON a.cd_agrupamento = p.cd_agrupamento
             WHERE p.cd_enquete = 634 
               AND p.dt_exclusao IS NULL
             ORDER BY p.nr_ordem, a.ordem, p.cd_pergunta;";

        $perguntas = $this->db->query($qr_sql)->result_array();

        echo '"GRUPO";"CD PERGUNTA";"PERGUNTA"'."\n";

        foreach ($perguntas as $key => $item) 
        {
        	echo '"'.$item['nome'].'";"'.$item['cd_pergunta'].'";"'.$item['texto'].'"'."\r\n";
        }
	}

	public function respostas()
	{
		$qr_sql = "
			SELECT * 
			  FROM projetos.enquete_perguntas p
			  JOIN projetos.enquete_agrupamentos a
                ON a.cd_agrupamento = p.cd_agrupamento
			 WHERE p.cd_enquete = 644 
			   AND p.dt_exclusao IS NULL
			 ORDER BY p.nr_ordem, a.ordem, p.cd_pergunta;";

		$collection = $this->db->query($qr_sql)->result_array();

		$cabecalho = '';

		$perguntas = array();

		foreach ($collection as $key => $item) 
		{
			$perguntas[$item['cd_pergunta']] = $item;

			$cabecalho .= '"'.$item['cd_pergunta'].'";"'.$item['cd_pergunta'].'_COMPL";';
		}

		echo '"NR";"IDENTIFICADOR";'.$cabecalho."\r\n";

		$i = 1;

		$qr_sql = "
			SELECT DISTINCT ip 
			  FROM projetos.enquete_resultados 
			 WHERE cd_enquete = 644;";

		$respostas = $this->db->query($qr_sql)->result_array();

		foreach ($respostas as $key => $item) 
        {
        	echo '"'.$i.'";"'.$item['ip'].'";';

        	$i++;

        	$qr_sql = "
        		SELECT er.questao, er.valor, er.complemento, ep.cd_pergunta, er.descricao
                  FROM projetos.enquete_resultados er
                  LEFT JOIN projetos.enquete_perguntas ep
                    ON 'R_' || ep.cd_pergunta::text = er.questao
                   AND ep.cd_enquete = er.cd_enquete
                  LEFT JOIN projetos.enquete_agrupamentos a
                    ON a.cd_agrupamento = ep.cd_agrupamento
                 WHERE er.cd_enquete = 644 
                   AND er.ip = '".trim($item['ip'])."' 
                 ORDER BY ep.nr_ordem, a.ordem, ep.cd_pergunta;";

            $participante_resultado = $this->db->query($qr_sql)->result_array();

            $linha = '"';

            foreach ($participante_resultado as $key2 => $item2) 
            {
            	if(trim($item2['questao']) == 'Texto')
            	{
            		echo '"'.trim($item2['descricao']).'";"";';
            	}
            	else
            	{
            		if(isset($participante_resultado[$key2+1]['cd_pergunta']) AND $participante_resultado[$key2+1]['cd_pergunta'] == $item2['cd_pergunta'])
            		{
						$linha .= $perguntas[$item2['cd_pergunta']]['rotulo'.$item2['valor']].',';
            		}
            		else
            		{
            			echo $linha.$perguntas[$item2['cd_pergunta']]['rotulo'.$item2['valor']].'";"'.$item2['complemento'].'";';
            			$linha = '"';
            		}
            	}
            }

            echo "\r\n";
        }

       
		
/*
		

		foreach ($respostas as $key => $item) 
        {
        	$qr_sql = "
        		SELECT er.questao, er.valor, er.complemento
                  FROM projetos.enquete_resultados er
                 WHERE er.cd_enquete = 634 
                   AND er.ip = '".trim($item['ip'])."' 
                 ORDER BY er.cd_agrupamento, er.questao;";

            $participante_resultado = $this->db->query($qr_sql)->result_array();

            echo '"'.$i.'";"'.$item['ip'].'";';

            foreach ($participante_resultado as $key2 => $item2) 
            {
            	if(trim($item2['questao']) == 'Texto')
            	{
            		echo '"'.trim($item2['valor']).'";';
            	}
            	else
            	{
	            	$a = explode('_', $item2['questao']);
	            	$cd_pergunta = $a[1];

	            	$qr_sql = "
						SELECT * 
						  FROM projetos.enquete_perguntas 
						 WHERE cd_enquete = 634 
						   AND dt_exclusao IS NULL
						   AND cd_pergunta = ".intval($cd_pergunta).";";

					$respostas = $this->db->query($qr_sql)->row_array();

					echo '"'.$respostas['legenda'.$item2['valor']].'";"'.$item2['complemento'].'";';
				}
            } 

            exit;

        	$i++;
        }

        */
	}
}