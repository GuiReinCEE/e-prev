<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
include_once("inc/sessao.php");
include_once("inc/conexao.php");

include_once("inc/ePrev.Service.Projetos.php");

class avaliacao_partial_lista
{   #begin_class
    private $db;
    private $filtrar;
    private $filtro;
    private $cd_usuario_logado;

    function avaliacao_partial_lista( $_db )
    {
        $this->db = $_db;
        $this->requestParams();
        $this->cd_usuario_logado = $_SESSION["Z"];
    }

    function __destruct()
    {
        $this->db = null;
    }

    function requestParams(){}

    public function get_cd_usuario_logado()
    {
        return $this->cd_usuario_logado;
    }

    public function loadLista()
    {
        $aval = new entity_projetos_avaliacao_capa_extended();

        $aval->set_cd_usuario_avaliado( $this->cd_usuario_logado );
        $aval->set_cd_usuario_avaliador( $this->cd_usuario_logado );
        $aval->set_dt_periodo(intval($_REQUEST['ano']));

        $srv = new service_projetos( $this->db );
        $capas = $srv->avaliacao_capa_FetchAll( $aval );
        $srv = null;

        return $capas;
    }

    public function get_integrantes_comite( $dt_periodo, $cd_usuario_avaliado )
    {
        $service = new service_projetos( $this->db );
        $result = $service->avaliacao_comite_ToString( $dt_periodo, $cd_usuario_avaliado );
        $service = null;

        return $result;
    }

    public function usuario_logado_is_comite($c)
    {
        $ret = false;
        if(!is_null($c))
        {
            foreach( $c->comite as $integrante )
            {
                if(! is_null($integrante) )
                {
                    if ($integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                    {
                        $ret = true;
        				break;
        			}
                }
            }
        }
        return $ret;
    }

    public function avaliado_comite($c)
    {
        $ret = false;
        if(!is_null($c))
        {
            foreach( $c->avaliacoes as $avaliacao )
            {
                if( !is_null($avaliacao) )
                {
                    if($avaliacao->get_tipo()=="C" && $avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                    {
                        $ret = true;
                        break;
                    }
                }
            }
        }

        return $ret;
    }

    public function avaliacao_comite_aberta($c)
    {
        $ret = false;
        if(!is_null($c))
        {
            foreach( $c->avaliacoes as $avaliacao )
            {
                if( !is_null($avaliacao) )
                {
                    if($avaliacao->get_tipo()=="C" && $avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                    {
                        $ret = ( $avaliacao->get_dt_conclusao()=='' );
                        break;
                    }
                }
            }
        }

        return $ret;
    }

    public function tem_avaliacao($c)
    {
        $ret = false;
        if(!is_null($c))
        {
            foreach( $c->avaliacoes as $avaliacao )
            {
                if(! is_null($avaliacao) )
                {
                    if ($avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                    {
                        $ret = true;
                        break;
                    }
                }
            }
        }
        return $ret;
    }
    
    public function get_status($c)
    {
        if($c->get_dt_publicacao()!='')
        {
            return '<span style="font-weight: bold; color: gray;">Avaliação Finalizada</span>';
        }
        else
        {
            if($c->get_status()=="A")
            {
                return '<span style="font-weight: normal; color: black;">Avaliação Iniciada</span>';
            }
            if($c->get_status()=="F")
            {
                return '<span style="font-weight: bold; color: blue;">Encaminhado ao Superior</span>';
            }
            if($c->get_status()=="E")
            {
                return '<span style="font-weight: bold; color: #903645;">Aguardando nomeação do Comitê</span>';
            }
            if($c->get_status()=="S")
            {
                return '<span style="font-weight: bold; color: #FB4C2F;">Encaminhado ao Comitê</span>';
            }
            if($c->get_status()=="C")
            {
                return '<span style="font-weight: bold; color: green;">Aprovado pelo Comitê</span>';
            }
        }
    }

    public function get_tipo_extenso($tipo)
    {
    	if($tipo=="V") return "Vertical";
    	if($tipo=="H") return "Horizontal";
    	if($tipo=="") return "Não Informado";
    }
    
    public function permite_iniciar_avaliacao_vertical()
    {
     	return ($_SESSION['INDIC_09']=='*');
    }

    public function gerente_da_area( entity_projetos_avaliacao_capa_extended $capa)
    {
		if( $this->cd_usuario_logado == $capa->get_cd_usuario_avaliador() )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
    public function tem_possibilidade_promocao( entity_projetos_avaliacao_capa_extended $capa)
    {
    	$service = new service_projetos($this->db);
    	if( $this->cd_usuario_logado==$capa->get_cd_usuario_avaliador() )
    	{
	    	if($capa->get_status()=="F")
	    	{
	    		if($capa->get_tipo_promocao()=="H")
	    		{
		    		if( $service->avaliacao__processo_completo($capa) )
		    		{
		    			$avaliado = $capa->avaliado;
						$usuario_matriz = $avaliado->usuario_matriz;
						$matriz_salarial = $usuario_matriz->matriz_salarial;
			    		$GRAU_MINIMO = $matriz_salarial->valor_final;
			    		$GRAU = 0;
			    		$hlp = new helper_avaliacao_resultado($this->db, $capa->get_cd_avaliacao_capa(), 0);
			    		$hlp->load();
				    	foreach($hlp->capa->avaliacoes as $avaliacao)
				    	{
					    	if( $avaliacao->get_tipo()=='S' )
				            {
								$hlp->load_valores( $avaliacao->get_cd_avaliacao() );
				                $GRAU = $hlp->get_grau_final();
				                break;
							}
				    	}
			    		return ( floatval($GRAU)>=floatval($GRAU_MINIMO) );
		    		}
		    		else
		    		{
		    			return false;
		    		}
	    		}
	    		else
	    		{
	    			// promoção vertical
	    			return false;
	    		}
	    	}
	    	else
	    	{
	    		return false;
	    	}
    	}
    	else
    	{
    		return false;
    	}
    }
} #end_class
$esta = new avaliacao_partial_lista($db);
$resultado = $esta->loadLista();
?>

    <center><a href="javascript:void(0)" onclick="thisPage.new_Click(this)">Nova Avaliação</a>
     <? if( $esta->permite_iniciar_avaliacao_vertical() ) : ?>
        | <a href="javascript:void(0)" onclick="thisPage.new_vertical_Click(this)" registroId="">Nova Avaliação para promoção Vertical</a>
     <? endif; ?>
    </center>

    <!-- ---------------------- -->
    <table style="width:100%">
        <tr>
        <td>

            <table align="center" style="width:90%">
                <tr><td>

					<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
					<thead>
					<tr> 
					<td width="10"></td>					
					<td height="30">Ano</td>					
					<td height="30">Avaliado</td>
					<td height="30">Status</td>
					
					<td height="30">Avaliador</td>
					<td height="30">Comitê</td>
					
					<td height="30">Tipo</td>
					</tr>
					</thead>
					<tbody>	
					<? foreach( $resultado as $capa ) : if(!is_null($capa)) :  ?>
					
						<? 
							if( $esta->tem_possibilidade_promocao($capa) )
							{
								$bgcolor="#FBE983";  
							}
							else if( ($capa->get_status()=="S"||$capa->get_status()=="C") && $esta->usuario_logado_is_comite($capa) && $esta->avaliado_comite($capa) && $esta->avaliacao_comite_aberta($capa) )
							{
								$bgcolor="#FBE983";
							}
							else
							{
								$bgcolor="";
							}
						?>

						<tr bgcolor="<?= $bgcolor ?>" 
							onmouseover="sortSetClassOver(this);" 
							onmouseout="sortSetClassOut(this);"
							marcar="<? if($bgcolor!="") echo("S"); else echo("N"); ?>"
							> 
						<td class="texto1" style="text-align:center;">
							<?
                            if (
                                                ( $capa->get_status()=="A" && $capa->get_cd_usuario_avaliado()==$esta->get_cd_usuario_logado() )
                                             || ( $capa->get_status()=="F" && $capa->get_cd_usuario_avaliador()==$esta->get_cd_usuario_logado() )
                                             || ( $capa->get_status()=="S" && $esta->usuario_logado_is_comite($capa) && !$esta->avaliado_comite($capa) )
                                           ) 
                                        {
							?>
											<!-- EXIBIR DETALHE - edit -->
                                            <a href='javascript:void(0)' 
                                                onclick='thisPage.edit_Click( this );'
                                                registroId='<?= $capa->get_cd_avaliacao_capa() ?>'
                                                status='<?= $capa->get_status() ?>'
                                                tipoPromocao=<?= $capa->get_tipo_promocao() ?>
                                                <? if($esta->usuario_logado_is_comite($capa)) { ?>
                                                    isComite='S'
                                                <? } else { ?>
                                                    isComite='N'
                                                <? } ?>
                                                <? if( $capa->get_dt_publicacao()!="" ) { ?>
                                                    publicada='S'
                                                <? } else { ?>
                                                    publicada='N'
                                                <? } ?>
                                                <? if($esta->tem_avaliacao($capa)){ ?>
                                                    temAvaliacao='S'
                                                <? } else { ?>
                                                    temAvaliacao='N'
                                                <? } ?>
                                                <? if($capa->get_cd_usuario_avaliado()==$esta->get_cd_usuario_logado()) { ?>
                                                    isAvaliado='S'
                                                <? } else { ?>
                                                    isAvaliado='N'
                                                <? } ?>
                                                ><img src='img/avaliacao_manutencao.png'
                                                    border='0' 
                                                    title='Editar avaliação'
                                                    /></a>
                                        <? }?>
                                        <? if ( 

                                               ( ($capa->get_status()=="E"||$capa->get_status()=="S"||$capa->get_status()=="F"||$capa->get_status()=="C") && $capa->get_cd_usuario_avaliado()==$esta->get_cd_usuario_logado() )
                                            || ( ($capa->get_status()=="E"||$capa->get_status()=="S"||$capa->get_status()=="C") && $capa->get_cd_usuario_avaliador()==$esta->get_cd_usuario_logado() )
                                            || ( ($capa->get_status()=="S"||$capa->get_status()=="C") && $esta->usuario_logado_is_comite($capa) && $esta->avaliado_comite($capa) )
											|| ( ($capa->get_status()=="S"||$capa->get_status()=="C") && ($esta->get_cd_usuario_logado() == $capa->get_cd_gerente_avaliado()) )
                                            )

                                            { ?>
                                            <!-- EXIBIR DETALHE - view -->
                                            <a href='javascript:void(0)' 
                                                onclick='thisPage.view_Click( this )' 
                                                registroId='<?= $capa->get_cd_avaliacao_capa() ?>'
                                                status='<?= $capa->get_status() ?>'
                                                tipoPromocao=<?= $capa->get_tipo_promocao() ?>
                                                <?if($esta->usuario_logado_is_comite($capa)){?>
                                                    isComite='S'
                                                <?}else{?>
                                                    isComite='N'
                                                <?}?>
                                                <?if( $capa->get_dt_publicacao()!="" ) {?>
                                                    publicada='S'
                                                <?}else{?>
                                                    publicada='N'
                                                <?}?>
                                                <?if($capa->get_cd_usuario_avaliado()==$esta->get_cd_usuario_logado()){?>
                                                    isAvaliado='S'
                                                <?}else{?>
                                                    isAvaliado='N'
                                                <?}?>
                                                temAvaliacao='S'
                                                ><img 
                                                
	                                                <? if( ($capa->get_status()=="S"||$capa->get_status()=="C") && $esta->usuario_logado_is_comite($capa) && $esta->avaliado_comite($capa) && $esta->avaliacao_comite_aberta($capa) ): ?>
		                                                src='img/avaliacao_manutencao.png'
	                                                    title='Editar avaliação'
	                                                <? else: ?>
		                                                src='img/avaliacao_visualizar.png'
	                                                    title='Ver detalhes da avaliação'
	                                                <? endif; ?>

                                                    border='0' 
                                                     
                                                    /></a>
                                        <? } ?>
						
						</td>
						<td class="texto1" style="text-align:center;"><?= $capa->get_dt_periodo() ?></td>
						<td class="texto1" style="text-align:left;"><?= $capa->avaliado->get_guerra() ?></td>
						
						<td class="texto1" style="text-align:center;"><?= $esta->get_status($capa); ?></td>
						
						<td class="texto1" style="text-align:left;"><?= $capa->avaliador->get_guerra() ?></td>
						<td class="texto1" style="text-align:center;">
						
											<?
                                            $virgula = "";
                                            foreach($capa->comite as $integrante) {
                                                
                                                if (!is_null($integrante) )
                                                {
                                                    echo( $virgula . $integrante->avaliador->get_guerra() );
                                                    $virgula = ", ";
                                                }
                                                
                                            }
                                            $integrante = null; 
                                            ?>
						
						</td>
						
						<td class="texto1" style="text-align:center;"><?= $esta->get_tipo_extenso($capa->get_tipo_promocao()); ?></td>
						</tr>
					<? endif; endforeach; ?>
					</tbody>
					</table>
				
					<br /><br />

                    
                </td></tr>
            </table>
        </td>
        </tr>
    </table>
    <div id="message_panel"></div>
    <!-- --------------------------- -->

<? $esta = null ?>