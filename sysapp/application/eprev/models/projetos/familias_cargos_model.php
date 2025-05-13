<?php
class Familias_cargos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_familia,
				   nome_familia
			  FROM projetos.familias_cargos
			 ORDER BY nome_familia;";

		$result = $this->db->query($qr_sql);
	}

	function carregar( &$result, $args=array() )
	{
        $qr_sql = "
			SELECT cd_familia,
				   nome_familia,
				   classe
			  FROM projetos.familias_cargos
			 WHERE cd_familia = ".intval($args['cd_familia']);

        $result = $this->db->query($qr_sql);
    }

    function escolaridade( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT cd_escolaridade,
				   nome_escolaridade
			  FROM projetos.escolaridade
			 ORDER BY nome_escolaridade;";
        $result = $this->db->query($qr_sql);
    }

    function carrega_escolaridade( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT nome_escolaridade
			  FROM projetos.escolaridade
			 WHERE cd_escolaridade = ".intval($args['cd_escolaridade']);

        $result = $this->db->query($qr_sql);
    }

    function familias_escolaridades(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_familia,
				   cd_escolaridade,
				   grau_percentual,
				   CASE WHEN nivel = 'B' THEN 'Básico'
						WHEN nivel = 'P' THEN 'Pleno'
						ELSE 'Excelente'
				   END  AS nivel
			  FROM projetos.familias_escolaridades
			 WHERE cd_familia = ".intval($args['cd_familia'])."";

        $result = $this->db->query($qr_sql);
    }

    function carrega_familias_escolaridades(&$result, $args=array())
    {
        $qr_sql = "
			SELECT grau_percentual,
				   nivel
			  FROM projetos.familias_escolaridades
			 WHERE cd_familia      = ".intval($args['cd_familia'])."
			   AND cd_escolaridade = ".intval($args['cd_escolaridade'])."";

        $result = $this->db->query($qr_sql);
    }

	function salvar(&$result, $args=array())
	{
        $retorno = $args['cd_familia'];

        if(intval($args['cd_familia']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.familias_cargos
                   SET nome_familia = ".(trim($args['nome_familia']) == '' ? 'DEFAULT' : "'".trim($args['nome_familia'])."'").",
                       dt_alteracao = CURRENT_TIMESTAMP,
                       classe =  ".(trim($args['classe']) == '' ? "DEFAULT" : "'".trim($args['classe'])."'").",
                       usu_alteracao = ".intval($args['usuario'])."
                 WHERE cd_familia =".intval($args['cd_familia']);
        }
        else
        {
            $new_id = intval($this->db->get_new_id("projetos.escolaridade", "cd_escolaridade"));

            $qr_sql = "
			    INSERT INTO projetos.familias_cargos
					 (
					    cd_familia,
					    nome_familia,
					    dt_inclusao,
					    classe
					 )
			    VALUES
					(
					    ".intval($new_id).",
					    ".(trim($args['nome_familia']) == '' ? "DEFAULT" : "'".trim($args['nome_familia'])."'").",
					    CURRENT_TIMESTAMP,
					    ".(trim($args['classe']) == '' ? "DEFAULT" : "'".trim($args['classe'])."'")."
					)";

            $retorno = $new_id;
            
        }
		
        $result = $this->db->query($qr_sql);
        return $retorno;

    }

    function salva_familia(&$result, $args=array())
    {
        if(intval($args['tipo']) == 0)
        {
            $qr_sql = "
			    INSERT INTO projetos.familias_escolaridades
					 (
					    cd_familia,
					    cd_escolaridade,
					    grau_percentual,
					    nivel
					 )
			   VALUES
					(
					    ".intval($args['cd_familia']).",
					    ".intval($args['cd_escolaridade']).",
					    ".intval($args['grau_percentual']).",
					    ".(trim($args['nivel']) == '' ? "DEFAULT" : "'".trim($args['nivel'])."'")."
					)";
        }
        else
        {
            $qr_sql = "
                UPDATE projetos.familias_escolaridades
                   SET grau_percentual =  ".intval($args['grau_percentual']).",
                       nivel           = ".(trim($args['nivel']) == '' ? "DEFAULT" : "'".trim($args['nivel'])."'")."
                 WHERE cd_familia      = ".intval($args['cd_familia'])."
                   AND cd_escolaridade = ".intval($args['cd_escolaridade']);
        }

        $result = $this->db->query($qr_sql);
    }
}
?>