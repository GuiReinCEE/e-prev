<?php
	set_title('Sistema de Avaliação - Matriz Quadro');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_matriz_quadro') ?>";
	}
</script>
<style>
	div.quadrado_matriz {
		width:110px;
		height:110px;
		border: 1px solid #000;
		float:left;
		line-height:15px;
		text-align: center;
        margin: 4px 4px 4px 4px;
	}

	div.quadrado_matriz.span {
		padding: 1px;
	}

    .texto_grupo {
        font-size: 20px;
        font-weight: bold;
        color:#1E1E1E;
    }
</style>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_matriz', 'Matriz', TRUE, 'location.reload();');

    echo aba_start($abas); 
        echo '
        	<center>
        		<table>';

        foreach ($collection as $key => $item) 
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
			            	<span style="color:'.$quadro['cor_texto'].';"><b>'.$quadro['cd_matriz'].'</b>'.br().nl2br($quadro['ds_matriz']).'</span>
			            </div>
        			</td>';
        		}

        	echo '</tr>';
        }
            echo '
                    <tr>
                        <td></td>
                        <td colspan="4" style="text-align:center; padding-top:15px;"><span class="texto_grupo">FATOR DE DESEMPENHO</span></td>
                    </tr>
        		</table>
        	</center>';
        echo br(2);
	echo aba_end();

    $this->load->view('footer_interna');
?>