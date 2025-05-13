<?php
class Docs_recebidos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT funcoes.nr_sg_documento_recebido(doc.ano::INTEGER, doc.numero::INTEGER) AS ano_numero,
				   doc.ano,
				   doc.numero,
	               TO_CHAR(doc.datahora, 'DD/MM/YYYY HH24:MI') AS datahora, 
	               doc.remetente, 
                   doc.assunto, 
	               doc.destino_emp, 
                   doc.destino_re, 
                   doc.destino_seq, 
	               doc.destino_nome
	          FROM projetos.docs_recebidos doc
			 WHERE doc.dt_exclusao IS NULL
			   AND ((doc.fl_restrito = 'S' AND funcoes.get_usuario_area(doc.cd_usuario_inclusao) = '".trim($args['cd_gerencia'])."') OR doc.fl_restrito = 'N')
			   ".(trim($args['ano']) != '' ? "AND doc.ano = ".intval($args['ano']) : '')."
               ".(trim($args['numero']) != '' ? "AND doc.numero = ".intval($args['numero']) : '')."
			   ".(trim($args['remetente']) != '' ? "AND UPPER(doc.remetente) LIKE UPPER('%".trim($args['remetente'])."%')" : '')."
			   ".(trim($args['destino']) != '' ? "AND UPPER(doc.destino_nome) LIKE UPPER('%".trim($args['destino'])."%')" : '')."
			   ".(((trim($args['data_ini']) != "") and  (trim($args['data_fim']) != "")) ? " AND DATE_TRUNC('day', doc.datahora) BETWEEN TO_DATE('".$args['data_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['data_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega($ano, $numero)
	{
		$qr_sql = "
			SELECT funcoes.nr_sg_documento_recebido(doc.ano::INTEGER, doc.numero::INTEGER) AS ano_numero,
				   doc.ano,
				   doc.numero,
	               TO_CHAR(doc.datahora, 'DD/MM/YYYY HH24:MI') AS data, 
	               TO_CHAR(doc.datahora, 'HH24:MI') AS hora, 
	               doc.remetente, 
                   doc.assunto, 
	               doc.destino_emp, 
                   doc.destino_re, 
                   doc.destino_seq, 
	               doc.destino_nome,
	               doc.fl_restrito,
				   funcoes.get_usuario_area(doc.cd_usuario_inclusao) AS cd_gerencia_inclusao
	          FROM projetos.docs_recebidos doc
			 WHERE doc.ano    = ".intval($ano)."
			   AND doc.numero = ".intval($numero).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.docs_recebidos 
				 (  
					datahora,
					remetente,
					assunto,
					destino_emp, 
                    destino_re, 
                    destino_seq, 
                    destino_nome,
                    fl_restrito,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".(trim($args['data']) != '' ? "TO_TIMESTAMP('".$args['data']." ".$args['hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
					".(trim($args['remetente']) != '' ? str_escape($args['remetente']) : "DEFAULT").",
					".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
					".(trim($args['destino_emp']) != '' ? intval($args['destino_emp']) : "DEFAULT").",
					".(trim($args['destino_re']) != '' ? intval($args['destino_re']) : "DEFAULT").",
					".(trim($args['destino_seq']) != '' ? intval($args['destino_seq']) : "DEFAULT").",		
					".(trim($args['destino_nome']) != '' ? str_escape($args['destino_nome']) : "DEFAULT").",				
					".(trim($args['fl_restrito']) != '' ? str_escape($args['fl_restrito']) : "DEFAULT").",				
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
	}

	public function atualizar($ano, $numero, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.docs_recebidos 
			   SET datahora             = ".(trim($args['data']) != '' ? "TO_TIMESTAMP('".$args['data']." ".$args['hora']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			       remetente            = ".(trim($args['remetente']) != '' ? str_escape($args['remetente']) : "DEFAULT").",
				   assunto              = ".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
				   destino_emp          = ".(trim($args['destino_emp']) != '' ? intval($args['destino_emp']) : "DEFAULT").",
                   destino_re           = ".(trim($args['destino_re']) != '' ? intval($args['destino_re']) : "DEFAULT").",
                   destino_seq          = ".(trim($args['destino_seq']) != '' ? intval($args['destino_seq']) : "DEFAULT").",
                   destino_nome         = ".(trim($args['destino_nome']) != '' ? str_escape($args['destino_nome']) : "DEFAULT").",
                   fl_restrito          = ".(trim($args['fl_restrito']) != '' ? str_escape($args['fl_restrito']) : "DEFAULT").",
				   cd_usuario_inclusao  = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE ano    = ".intval($ano)."
			   AND numero = ".intval($numero).";";

	   	$this->db->query($qr_sql);
	}

	public function excluir($ano, $numero, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.docs_recebidos
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE ano    = ".intval($ano)."
			   AND numero = ".intval($numero).";";

		$this->db->query($qr_sql);
	}

	public function listar_anexo($ano, $numero)
	{
		$qr_sql = "
			SELECT aa.cd_docs_recebidos_anexo,
				   aa.arquivo,
				   aa.arquivo_nome,
				   aa.cd_usuario_inclusao,
				   TO_CHAR(aa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(aa.cd_usuario_inclusao) AS ds_usuario
			  FROM projetos.docs_recebidos_anexo aa
			 WHERE aa.ano    = ".intval($ano)."
			   AND aa.numero = ".intval($numero)."
			   AND aa.dt_exclusao IS NULL
			 ORDER BY aa.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($ano, $numero, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.docs_recebidos_anexo
                 (
                    ano,
                    numero,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                    ".intval($ano).",
                    ".intval($numero).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }

    public function excluir_anexo($cd_correspondencia_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.docs_recebidos_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_docs_recebidos_anexo = ".intval($cd_correspondencia_anexo).";";

        $this->db->query($qr_sql);
    }
}
?>