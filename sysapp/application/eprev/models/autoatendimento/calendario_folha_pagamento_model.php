<?php

class Calendario_folha_pagamento_model extends Model {

	  function __construct()
    {
    	parent::Model();
    }

    public function listar($nr_ano)
    {
      $qr_sql = "
          SELECT cd_calendario_folha_pagamento,
          		   ds_calendario_folha_pagamento,
                 TO_CHAR(dt_calendario_folha_pagamento, 'DD/MM/YYYY') AS dt_calendario_folha_pagamento
            FROM autoatendimento.calendario_folha_pagamento
           WHERE dt_exclusao IS NULL
             AND TO_CHAR(dt_calendario_folha_pagamento, 'YYYY') = ('".trim($nr_ano)."')";
  
      return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_calendario_folha_pagamento)
    {
        $qr_sql = "
            SELECT cd_calendario_folha_pagamento,
                   ds_calendario_folha_pagamento, 
                   TO_CHAR(dt_calendario_folha_pagamento, 'DD/MM/YYYY') AS dt_calendario_folha_pagamento
              FROM autoatendimento.calendario_folha_pagamento 
             WHERE dt_exclusao IS NULL
               AND cd_calendario_folha_pagamento = ".intval($cd_calendario_folha_pagamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
      $cd_calendario_folha_pagamento = intval($this->db->get_new_id('autoatendimento.calendario_folha_pagamento', 'cd_calendario_folha_pagamento'));

      $qr_sql = "
        INSERT INTO autoatendimento.calendario_folha_pagamento
             (
                cd_calendario_folha_pagamento, 
                ds_calendario_folha_pagamento, 
                dt_calendario_folha_pagamento,
                cd_usuario_inclusao,
                cd_usuario_alteracao
             )
        VALUES 
             (
                ".intval($cd_calendario_folha_pagamento).",
                ".(trim($args['ds_calendario_folha_pagamento']) != '' ? str_escape($args['ds_calendario_folha_pagamento']): 'DEFAULT').",
                ".(trim($args['dt_calendario_folha_pagamento']) != '' ? "TO_DATE('".trim($args['dt_calendario_folha_pagamento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
  
        return $cd_calendario_folha_pagamento;
    }

    public function atualizar($cd_calendario_folha_pagamento, $args = array())
    {
      $qr_sql = "
          UPDATE autoatendimento.calendario_folha_pagamento
             SET ds_calendario_folha_pagamento = ".(trim($args['ds_calendario_folha_pagamento']) != '' ? str_escape($args['ds_calendario_folha_pagamento']): 'DEFAULT').",
                 dt_calendario_folha_pagamento = ".(trim($args['dt_calendario_folha_pagamento']) != '' ? "TO_DATE('".trim($args['dt_calendario_folha_pagamento'])."', 'DD/MM/YYYY')" : "DEFAULT").",              
                 cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
                 dt_alteracao                 = CURRENT_TIMESTAMP
           WHERE cd_calendario_folha_pagamento  = ".intval($cd_calendario_folha_pagamento).";";

      $this->db->query($qr_sql);
    }
}    
?>