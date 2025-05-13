<?php
/**
 * atende as tabelas projetos.familias_cargos e suas relacionadas como matriz_salarial
 */
class ADO_projetos_familias_cargos
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_familias_cargos( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    }

    public function fetch_all()
    {
        $this->dal->createQuery("

            SELECT *
              FROM projetos.familias_cargos
          ORDER BY classe

        ");

        $resultados = $this->dal->getResultset();

        $familias = array();        
        while ($row = pg_fetch_array($resultados))
        {
			$familia = new entity_projetos_familias_cargos();

            $familia->set_cd_familia( $row['cd_familia'] );
            $familia->set_nome_familia( $row['nome_familia'] );
            $familia->set_classe( $row['classe'] );
            
            $familias[ sizeof($familias) ] = $familia;
		}

        return $familias;
    }
    
    public function salvar_matriz( $matriz )
    {
        if( $matriz['cd_matriz_salarial']!='0' && $matriz['cd_matriz_salarial']!='' )
        {
            $this->dal->createQuery("
    
                UPDATE projetos.matriz_salarial
                   SET cd_familias_cargos = {cd_familias_cargos}
                     , faixa = '{faixa}'
                     , valor_inicial = {valor_inicial}
                     , valor_final = {valor_final}
                 WHERE cd_matriz_salarial = {cd_matriz_salarial}
    
            ");
        }
        else
        {
            $this->dal->createQuery("
    
                INSERT INTO projetos.matriz_salarial
                            (cd_familias_cargos, faixa, valor_inicial, valor_final) 
                     VALUES ({cd_familias_cargos}, '{faixa}', {valor_inicial}, {valor_final})
    
            ");
        }

        $this->dal->setAttribute( '{cd_matriz_salarial}', (int)$matriz['cd_matriz_salarial'] );
        $this->dal->setAttribute( '{cd_familias_cargos}', (int)$matriz['cd_familias_cargos'] );
        $this->dal->setAttribute( '{faixa}', $matriz['faixa'] );
        $this->dal->setAttribute( '{valor_inicial}', str_replace(',', '.', $matriz['valor_inicial']) );
        $this->dal->setAttribute( '{valor_final}', str_replace(',', '.', $matriz['valor_final']) );
        $rt = $this->dal->executeQuery();

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_projetos_familias_cargos.salvar_matriz() ao executar comando SQL de consulta. ".$this->dal->getMessage());
        }

        return $rt;
    }
    
    public function fetch_matriz( $cd_familias_cargos, $faixa )
    {
         $this->dal->createQuery("

            SELECT *
              FROM projetos.matriz_salarial
             WHERE cd_familias_cargos = {cd_familias_cargos}
               AND faixa = '{faixa}'

        ");

        $this->dal->setAttribute( '{cd_familias_cargos}', (int)$cd_familias_cargos );
        $this->dal->setAttribute( '{faixa}', $faixa );
        $resultados = $this->dal->getResultset();
        
        if ($row = pg_fetch_array($resultados))
        {
            $matriz = array( 'cd_matriz_salarial'=>$row['cd_matriz_salarial'], 'valor_inicial'=>$row['valor_inicial'], 'valor_final'=>$row['valor_final'] );
        }
        else
        {
            $matriz = array( 'cd_matriz_salarial'=>'','valor_inicial'=>'', 'valor_final'=>'' );
        }

        return $matriz;
    }
    
    public function fetch_matriz_all()
    {
         $matrizes = array();
         $this->dal->createQuery("

            SELECT pms.*, pfc.nome_familia, pfc.classe
              FROM projetos.matriz_salarial pms
        INNER JOIN projetos.familias_cargos pfc
                   ON pms.cd_familias_cargos = pfc.cd_familia
          ORDER BY pfc.classe, pms.faixa

        ");

        $result = $this->dal->getResultset();
        
        while( $row = pg_fetch_array( $result ) )
        {
            $matriz = new entity_projetos_matriz_salarial_extended();
            $matriz->cd_matriz_salarial = $row['cd_matriz_salarial'];
            $matriz->cd_familias_cargos = $row['cd_familias_cargos'];
            $matriz->faixa = $row['faixa'];
            
            $familias_cargos = new entity_projetos_familias_cargos();
            $familias_cargos->set_nome_familia( $row['nome_familia'] );
            $familias_cargos->set_classe( $row['classe'] );
            
            $matriz->familias_cargos = $familias_cargos;
            
            $matrizes[ sizeof($matrizes) ] = $matriz;
        }

        return $matrizes;
    }

    public function fetch_faixas()
    {
         $faixas = array();
         $this->dal->createQuery("

            SELECT DISTINCT faixa
              FROM projetos.matriz_salarial
          ORDER BY faixa

        ");

        $result = $this->dal->getResultset();
        
        while( $row = pg_fetch_array( $result ) )
        {
            $faixa = new entity_projetos_matriz_salarial();
            $faixa->faixa = $row['faixa'];

            $faixas[ sizeof($faixas) ] = $faixa;
        }

        return $faixas;
    }
}
?>