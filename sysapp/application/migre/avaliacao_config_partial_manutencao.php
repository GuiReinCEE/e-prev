<?
header("Content-Type: text/html; charset=iso-8859-1");
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');

class avaliacao_config_partial_manutencao
{
    private $service;
    private $filtro;
    public $command;
    public $capas;
    public $gerencias;

    function __construct( $db )
    {
        $this->service = new service_projetos( $db );
        $this->filtro = new helper__avaliacao_capa__fetch_by_filter__filter();
        $this->gerencias = array();
        $this->requestParams();

        if($this->command=="filtrar")
        {
            $this->do_list_render__ajax();
        }
        if($this->command=="carregar_combo_avaliado")
        {
            $this->do_list_avaliados_render__ajax();
        }
        if($this->command=="reabrir_avaliacao")
        {
            $this->reabrir_avaliacao();
        }
        if($this->command=="encerrar_avaliacao")
        {
            $this->encerrar_avaliacao();
        }
        if($this->command=="excluir_avaliacao")
        {
            $this->excluir_avaliacao();
        }
    }

    private function requestParams()
    {
        if(isset($_POST["ajax_command_hidden"]))
        {
            $this->command = $_POST["ajax_command_hidden"];
        }
        if(isset($_POST["dt_periodo_text"]))
        {
        	$this->filtro->dt_periodo = $_POST["dt_periodo_text"];
        }
        if(isset($_POST["gerencia_select"]))
        {
        	$this->filtro->gerencia = $_POST["gerencia_select"];
        }
        if(isset($_POST["avaliado_select"]))
        {
        	$this->filtro->avaliado = $_POST["avaliado_select"];
        }
    }

    public function load()
    {
    	$this->capas = $this->service->projetos__avaliacao_capa__listar_todas_avaliadas_pelo_superior();
    	$this->gerencias = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
    }

    private function do_list_render__ajax()
    {
    	$this->capas = $this->service->projetos__avaliacao_capa__listar_todas_avaliadas_pelo_superior();
    	$output = '

	    	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
	    		<thead>
				<tr>
					<td><b>Nome do avaliado</b></td>
					<td><b>Nome do avaliador</b></td>
					<td><b>Período</b></td>
					<td><b>Resultado final</b></td>
				</tr>
	    		</thead>
				<tbody>

    	';

		$capa = new helper__avaliacao_capa__fetch_by_filter__entity();
		foreach ($this->capas as $capa)
		{
			$output .= '

				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td>' . $capa->nome_avaliado . '</td>
					<td>' . $capa->nome_avaliador . '</td>
					<td>' . $capa->periodo . '</td>
					<td>' . $capa->resultado_final . '</td>
				</tr>

			';
		}

    	$output .= '
			</tbody>
			</table>
    	';

    	echo $output;
    }
    
    private function do_list_avaliados_render__ajax()
    {
    	$this->gerencias = $this->service->usuarios_controledi__listar_agrupando_por_gerencia();
    	$output = '

    		<select name="avaliado_select" id="avaliado_select" style="width:300px;">
				<option value="0">:: selecione ::</option>

		';

    	$gerencia = new helper_usuarios_agrupados_por_divisao();
    	$usuario = new entity_projetos_usuarios_controledi_extended();
		foreach( $this->gerencias as $gerencia )
		{
			if( $gerencia->divisao == $this->filtro->gerencia )
			{
				foreach( $gerencia->usuarios as $usuario )
				{
			    	$output .= '

						<option value="' . $usuario->get_codigo() . '">' . $usuario->get_nome() . '</option>

			    	';
				}
				break;
			}
		}

    	$output .= '

			</select>

    	';

    	echo $output;
    }

    private function reabrir_avaliacao()
    {
    	$ret = $this->service->avaliacao_capa__modificar_avaliacao( 'REABRIR', $_POST['pk__hidden'] );

    	if( $ret )
    	{
	    	echo 'true';
    	}
    	else
    	{
    		echo 'false';
    	}
    }

    private function encerrar_avaliacao()
    {
    	$ret = $this->service->avaliacao_capa__modificar_avaliacao( 'ENCERRAR', $_POST['pk__hidden'] );

    	if( $ret )
    	{
	    	echo 'true';
    	}
    	else
    	{
    		echo 'false';
    	}
    }

    private function excluir_avaliacao()
    {
    	$ret = $this->service->avaliacao_capa__modificar_avaliacao( 'EXCLUIR', $_POST['pk__hidden'] );

    	if( $ret )
    	{
	    	echo 'true';
    	}
    	else
    	{
    		echo 'false';
    	}
    }
}

$esta = new avaliacao_config_partial_manutencao( $db );

if( $esta->command!='' )
{
   exit();
}

$esta->load();
?>
<input type="hidden" id="pk__hidden" name="pk__hidden" />
<div id="message_panel"></div>
<CENTER>

	<div id='lista_div'>
		<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
			<thead>
				<tr>
					<td><b>Nome do avaliado</b></td>
					<td><b>Nome do avaliador</b></td>
					<td><b>Período</b></td>
					<td><b>Tipo</b></td>
					<td><b>Resultado final</b></td>
					<td><b>Status</b></td>
					<td><b>Ações</b></td>
				</tr>
			</thead>
			<tbody>
			<? $capa = new helper__avaliacao_capa__fetch_by_filter__entity(); ?>
			<? foreach ($esta->capas as $capa) : ?>
				<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
					<td><?= $capa->nome_avaliado; ?></td>
					<td><?= $capa->nome_avaliador; ?></td>
					<td><?= $capa->periodo; ?></td>
					<td><? if($capa->tipo_promocao=="V") { echo('Vertical'); } else { echo('Horizontal'); } ?></td>
					<td><?= $capa->resultado_final; ?></td>
					<td><?php 
						if($capa->status=='A'){echo 'Aberta para o Avaliado';} 
						if($capa->status=='F'){echo 'Encaminhada ao Superior';} 
						if($capa->status=='S'){echo 'Fechada pelo Superior';} 
						if($capa->status=='E'){echo 'Nomeando Comitê';} 
						if($capa->status=='C'){echo 'Processo encerrado';} 
					?></td>
					<td align="center">
						
						<?php if($capa->status=='E' || $capa->status=='S'): ?>
							<a title='Reabre a avaliação para o Superior.' href="javascript:void(0)" onclick="esta.reabrir_avaliacao__Click(this)" registroId="<?= $capa->cd_avaliacao_capa; ?>">reabrir</a>
							|
							<a title='Encerra o processo de avaliação preenchendo a data de publicação.' href="javascript:void(0)" onclick="esta.encerrar_avaliacao__Click(this)" registroId="<?= $capa->cd_avaliacao_capa; ?>">encerrar</a>
							|
						<?php endif; ?>
						
						<?php /* status E:fechado superior e encaminhado ao comite | status S:fechado pelo superior */
						// echo $capa->status;
						if($capa->status=='A' || $capa->status=='F' || $capa->status=='E' || $capa->status=='S'): ?>
							<a title='Exclui a avaliação para novo início, apenas informações históricas serão mantidas.' href="javascript:void(0)" onclick="esta.excluir_avaliacao__Click(this)" registroId="<?= $capa->cd_avaliacao_capa; ?>">excluir</a>
						<?php endif; ?>
						
					</td>
				</tr>
			<? endforeach; ?>
			</tbody>
		</table>
	</div>
	
</CENTER>