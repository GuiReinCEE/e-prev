<?php
class Cargos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
            SELECT c.cd_cargo,
                   f.nome_familia,
                   c.nome_cargo
              FROM projetos.cargos c
              JOIN projetos.familias_cargos f
                ON c.cd_familia = f.cd_familia
             WHERE UPPER(c.nome_cargo) LIKE UPPER('%".trim($args["nome_cargo"])."%')
			 ".(trim($args['cd_familia']) != '' ? "AND c.cd_familia = ".intval($args['cd_familia']) : "").";";
		$result = $this->db->query($qr_sql);
	}

	function carregar( &$result, $args=array() )
	{
        $qr_sql = "
			SELECT cd_cargo,
                   nome_cargo,
                   desc_cargo,
                   cd_familia
              FROM projetos.cargos 
             WHERE cd_cargo=".intval($args['cd_cargo']);

        $result = $this->db->query($qr_sql);
	}

    function familia ( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT c.cd_familia AS value,
                   c.nome_familia AS text
              FROM projetos.familias_cargos c
             ORDER BY c.nome_familia";

        $result = $this->db->query($qr_sql);
    }

	function salvar( &$result, $args=array() )
	{    
        $retorno = $args['cd_cargo'];
        
        if($args['cd_cargo'] > 0)
        {
            $qr_sql = "
                UPDATE projetos.cargos
                   SET nome_cargo = ".(trim($args['nome_cargo']) == '' ? 'DEFAULT' : "'".trim($args['nome_cargo'])."'").",
                       desc_cargo = ".(trim($args['desc_cargo']) == '' ? 'DEFAULT' : "'".trim($args['desc_cargo'])."'").",
                       cd_familia = ".intval($args['cd_familia'])."
                 WHERE cd_cargo =".intval($args['cd_cargo']);
				 
            $result = $this->db->query($qr_sql);

            if(count($args['institucionais']) > 0)
			{
                $qr_sql = "
					DELETE 
					  FROM projetos.cargos_comp_inst
                     WHERE cd_cargo =".intval($args['cd_cargo']);
								 
                $this->db->query($qr_sql);

                for($i=0; $i< count($args['institucionais']); $i++)
                {
                    if(intval($args['institucionais'][$i]) != 0)
                    {
                        $qr_sql = "
							INSERT INTO projetos.cargos_comp_inst
                                 (
                                    cd_cargo,
                                    cd_comp_inst
                                 )
                            VALUES
                                 (
                                    ".intval($args['cd_cargo']).",
                                    ".intval($args['institucionais'][$i])."
                                 );";
                         $this->db->query($qr_sql);
                    }
                }
            }

            if(count($args['especificas']) > 0)
			{
                $qr_sql = "
					DELETE 
					  FROM projetos.cargos_comp_espec
                     WHERE cd_cargo =".intval($args['cd_cargo']);
					 
                $this->db->query($qr_sql);

                for($i=0; $i< count($args['especificas']); $i++)
                {
                    if(intval($args['especificas'][$i]) != 0)
                    {
                        $qr_sql = "
							INSERT INTO projetos.cargos_comp_espec
                                 (
                                    cd_cargo,
                                    cd_comp_espec
                                 )
                            VALUES
                                 (
                                    ".intval($args['cd_cargo']).",
                                    ".intval($args['especificas'][$i])."
                                 )";
                         $this->db->query($qr_sql);
                    }
                }
            }

            if(count($args['responsabilidades']) > 0)
			{
                $qr_sql = "
					DELETE 
					  FROM projetos.cargos_responsabilidades
                     WHERE cd_cargo =".intval($args['cd_cargo']);
                $this->db->query($qr_sql);

                for($i=0; $i< count($args['responsabilidades']); $i++)
                {
                    if(intval($args['responsabilidades'][$i]) != 0)
                    {
                        $qr_sql = "
							INSERT INTO projetos.cargos_responsabilidades
                                 (
                                    cd_cargo,
                                    cd_responsabilidade
                                 )
                            VALUES
                                 (
                                    ".intval($args['cd_cargo']).",
                                    ".intval($args['responsabilidades'][$i])."
                                 )";
                        $this->db->query($qr_sql);
                    }
                }
            }

        }
        else
        {
            $new_id = intval($this->db->get_new_id("projetos.cargos", "cd_cargo"));

            $qr_sql = "
			    INSERT INTO projetos.cargos
				     (
						cd_cargo,
					    nome_cargo,
						desc_cargo,
						cd_familia
					 )
				VALUES
					 (
					    ".intval($new_id).",
						".(trim($args['nome_cargo']) == '' ? "DEFAULT" : "'".trim($args['nome_cargo'])."'").",
						".(trim($args['desc_cargo']) == '' ? "DEFAULT" : "'".trim($args['desc_cargo'])."'").",
						".intval($args['cd_familia'])."
					 )";
            $result = $this->db->query($qr_sql);

            $retorno = $new_id;
        }

        return $retorno;
	}

    function competencias_institucionais( &$result, $args=array() )
    {
        $qr_sql = "
		   SELECT cd_comp_inst   AS value,
				  nome_comp_inst AS text
			 FROM projetos.comp_inst
			ORDER BY nome_comp_inst";

        $result = $this->db->query($qr_sql);
    }

    function competencias_institucionais_chk( &$result, $args=array() )
    {
        $qr_sql = "
			SELECT ci.cd_comp_inst
			  FROM projetos.cargos_comp_inst cci
			  JOIN projetos.comp_inst ci
				ON ci.cd_comp_inst = cci.cd_comp_inst
			   AND cci.cd_cargo = ".$args['cd_cargo']."
			 ORDER BY ci.nome_comp_inst";

        $result = $this->db->query($qr_sql);
    }

    function competencias_especificas(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_comp_espec   AS value,
				   nome_comp_espec AS text
			  FROM projetos.comp_espec
			 ORDER BY nome_comp_espec";

        $result = $this->db->query($qr_sql);
    }

    function competencias_especificas_chk(&$result, $args=array())
    {
        $qr_sql = "
			SELECT ce.cd_comp_espec
			  FROM projetos.cargos_comp_espec cce
			  JOIN projetos.comp_espec ce
				ON ce.cd_comp_espec = cce.cd_comp_espec
			 WHERE cce.cd_cargo = ".$args['cd_cargo']."
			 ORDER BY ce.nome_comp_espec";

        $result = $this->db->query($qr_sql);
    }

    function responsabilidades(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_responsabilidade   AS value,
				   nome_responsabilidade AS text
			  FROM projetos.responsabilidades
			 ORDER BY nome_responsabilidade;";

        $result = $this->db->query($qr_sql);
    }

    function responsabilidades_chk(&$result, $args=array())
    {
        $qr_sql = "
			SELECT ci.cd_responsabilidade
			  FROM projetos.cargos_responsabilidades cci
			  JOIN projetos.responsabilidades ci
				ON ci.cd_responsabilidade = cci.cd_responsabilidade
			 WHERE cci.cd_cargo = ".$args['cd_cargo']."
			 ORDER BY ci.nome_responsabilidade;";
        $result = $this->db->query($qr_sql);
    }
}
?>