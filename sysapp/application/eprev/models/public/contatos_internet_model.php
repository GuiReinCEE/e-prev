<?php
class Contatos_internet_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, &$count, $args=array() )
	{
		// mount query
		$sql = "
            SELECT codigo,
		           nome,
		           TO_CHAR(data, 'DD/MM/YYYY ') || hora AS data,
		           CASE WHEN resposta IS NULL
		                THEN 'N'
		                ELSE 'S'
		           END AS fl_respondido,
		           TO_CHAR(dt_resposta, 'DD/MM/YYYY') AS dt_resposta
		      FROM public.contatos_internet
		     WHERE dt_exclusao IS NULL
		       AND extract( 'years' from data ) = ".intval($args["ano"])."
		";

		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}

    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo,
                   nome,
                   endereco,
                   bairro,
                   cep,
                   cidade,
                   estado,
                   ddd,
                   telefone,
                   ramal,
                   fax,
                   email,
                   comentario,
                   resposta,
                   TO_CHAR(data, 'dd/mm/yyyy') AS data,
                   hora,
                   CASE WHEN resposta IS NULL
                        THEN 'N'
                        ELSE 'S'
                   END AS respondido,
                   cd_atendimento,
                   empresa,
                   re
              FROM public.contatos_internet
             WHERE codigo = ".intval($args['codigo'])."
            ";

        #echo "<pre>$qr_sql</pre>";
 
        $result = $this->db->query($qr_sql);
    }

    function carregaTipoAtendimento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   descricao AS text
              FROM public.listas
             WHERE categoria='TPCT'
             ORDER BY descricao
            ";

        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {

        $qr_sql ="
            UPDATE public.contatos_internet
               SET resposta       = '".$args['resposta']."',
                   dt_resposta    = CURRENT_TIMESTAMP,
                   usuario        = '".trim($args['usuario'])."',
                   cd_atendimento = '".trim($args['cd_tipo_atendimento'])."'
             WHERE codigo = ".intval($args['codigo']).";
            ";

        if($args['fl_envia_email'] == 'S')
        {
            $qr_sql .="
                INSERT INTO projetos.envia_emails
						  (
						    dt_envio,
							de,
							para,
							cc,
							cco,
							assunto,
							texto
						  )
					 VALUES
				      	  (
							CURRENT_TIMESTAMP,
							'FUNDAÇÃO CEEE - Atendimento',
							(
                             SELECT TRIM(email)
							   FROM contatos_internet
							  WHERE codigo = ".intval($args['codigo'])."
                            ),
							'',
							'',
							'Resposta da sua sugestão [Contato nº ".intval($args['codigo'])."]',
							'Prezada(o) ' || (
                                              SELECT TRIM(nome)
											    FROM contatos_internet
											   WHERE codigo = ".intval($args['codigo'])."
                                              )
							|| '".chr(10).chr(13)."' || (
                                                         SELECT TRIM(resposta)
													       FROM contatos_internet
													      WHERE codigo = ".intval($args['codigo'])."
                                                         )
						  );
            ";
        }
        echo '<pre>'.$qr_sql.'</pre>';

        $result = $this->db->query($qr_sql);
    }
}
