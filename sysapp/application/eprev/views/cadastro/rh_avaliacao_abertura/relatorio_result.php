<?php
	echo '
	<center>
		<table>';

	foreach ($matriz as $key => $item) 
	{
		echo '<tr>';

			if(intval($key) == 1)
            {
                echo '<td rowspan="'.count($item).'" style="text-align:center; padding-right:15px;"><span class="texto_grupo">C<br/>O<br/>M<br/>P<br/>E<br/>T<br/>Ê<br/>N<br/>C<br/>I<br/>A</span></td>';
            }

			foreach ($item as $key2 => $quadro) 
			{
				echo '
				<td>

	    			<div class="quadrado_matriz" style="background-color: '.$quadro['cor_fundo'].';">
		            	<span class="span_matriz" style="color:'.$quadro['cor_texto'].';"><b>'.$quadro['cd_matriz'].'</b>'.br().nl2br($quadro['ds_matriz']).'</span>
		            </div>
		            <div class="circulo_resultado">'.$quadro['nr_resultado'].'</div>

				</td>';
			}

		echo '</tr>';
	}

	echo '
            <tr>
                <td></td>
                <td colspan="4" style="text-align:center; padding-top:15px;"><span class="texto_grupo">FATOR DE DESEMPENHO</span></td>
            </tr>
		</table>';

	echo br(2);

	echo '</center>';

	$head = array( 
        'Gerência',
        '',
        'Nome',
        'Dt. Admissão',
        'Cargo / Área de Atuação',
        'Classe / Padrão',
        'Progressão',
        'Promoção'
    );

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->view_count = false;
    
	foreach ($ordem_ranking as $key => $item) 
	{
		$quadro = $collection[$key];

		if(count($quadro['avaliacao']) > 0)
		{
			echo '<h1 style="font-size:16px;">'.$quadro['cd_matriz'].' - '.nl2br($quadro['ds_matriz']).'</h1>'.br();

			$body = array();

		    foreach ($quadro['avaliacao'] as $key3 => $avaliacao) 
		    {
		    	$avatar_arquivo = trim($avaliacao['avatar']);
				
				if(trim($avatar_arquivo) == '')
				{
					$avatar_arquivo = $avaliacao['usuario'].'.png';
				}
				
				if(!file_exists('./up/avatar/'.$avatar_arquivo))
				{
					$avatar_arquivo = 'user.png';
				}

				$body[] = array(
					$avaliacao['ds_gerencia'],
					'<a href="'.site_url('cadastro/avatar/index/'.intval($avaliacao['cd_usuario'])).'" title="Clique aqui para ajustar a foto"><img width="50" src="'.base_url().'up/avatar/'.$avatar_arquivo.'"></a>',
					array($avaliacao['ds_nome'].br(2).$avaliacao['ds_usuario'], 'text-align:left;'),
					$avaliacao['dt_admissao'],
					array($avaliacao['ds_cargo_area_atuacao'], 'text-align:left'),
					$avaliacao['ds_classe'].(trim($avaliacao['ds_padrao']) != '' ? ' - '.$avaliacao['ds_padrao']: ''),
					$quadro['ds_progressao'],
					$quadro['ds_promocao']
		        );
		    }

		    $grid->body = $body;
		    echo $grid->render();
		}
	}	