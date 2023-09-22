<?php
session_start();

function celula($valor, $nome){
    if ($valor == 2 && !isset($_SESSION["som"])) {
        return '<a href="core.php?link='.$nome.'"><img src="./imagens/2.png" style="border:none"></a>';
    } else {
        return '<img src="./imagens/'.$valor.'.png">';
    }
}

function montarTabela($mascara){
    $contador = 0;
    for ($i = 0; $i < 3; $i ++) {
        $html .= "<tr>";
        for ($y = 0; $y < 3; $y ++) {
            $html .= "<td>" . celula($mascara[$contador], $contador) . "</td>";
            ++ $contador;
        }
        $html .= "</tr>";
    }
    return $html;
}

// FUNCÇÕES RESPONSAVEIS POR VALIDAR AS JOGADOAS
function validarJogadas($combinacoes){
    $result = false;
    foreach ($combinacoes as $combinacao){
        if (checarCombinacoes($combinacao, 'o') || checarCombinacoes($combinacao, 'x')) {
            $result = true;
            break;
        }
    }
    return $result;
}

function checarCombinacoes($combinacao, $valor) {
    if (($_SESSION["mascara"][$combinacao[0]] == $valor) && ($_SESSION["mascara"][$combinacao[1]] == $valor) && ($_SESSION["mascara"][$combinacao[2]] == $valor)) {
        return montarVitorioso($combinacao, ($valor == 'o') ? 3 : 4);
    } else {
        return false;
    }
}

function montarVitorioso($combinacao, $codigo ){
    foreach ($combinacao as $valor){
        $_SESSION["mascara"][$valor] = $codigo;
    }
    return true;
}

// RESPONSAVEL PELO EMPATE
function checarFinalJogo($fim) {    
    
    if (($_SESSION["contador"] <= 0) && ($fim == false)) {
        
        for ($i = 0; $i <= 8; $i ++) {
            if ($_SESSION["mascara"][$i] == 'o') {
                $_SESSION["mascara"][$i] = 3;
            } else {
                if ($_SESSION["mascara"][$i] == 'x') {
                    $_SESSION["mascara"][$i] = 4;
                }
            }
        }
        
        $_SESSION["som"] = '<EMBED SRC="./som/empate.wav" hidden="true"></EMBED>';
    } else if ($fim == true) {
        $_SESSION["som"] = '<EMBED SRC="./som/vitoria.wav" hidden="true"></EMBED>';
    }
    
    return $fim;
}

function sortearPosicaoVazia($mascara){
    $filtred = array();
    foreach ($mascara as $chave => $mask) {
        if ($mask == 2) {
            $filtred[$chave] = $chave;
        }
    }
    return array_rand($filtred);
}

function primeiraSelecao($mascara){
    $count = 0;
    foreach ($mascara as $mask) {
        if ($mask == 2) {
            ++$count;
        }
    }
    return ($count == 8) ? true : false;
}

function sortearPrimeiraJogadaIa($mascara, $link) {
    if ($mascara[1] != 2 || $mascara[3] != 2 || $mascara[5] != 2 || $mascara[7] != 2) {
        return 4;
    }
    
    if ($mascara[4] != 2) {
        return sortearPosicaoVazia($mascara);
    }
    
    $jogadas = array(
        array(0,2,8,6,4),
        array(2,0,6,8,4),
        array(6,0,2,8,4),
        array(8,0,2,6,4));
    foreach ($jogadas as $jogada) {
        if ($link == $jogada[0]) {
            return $jogada[rand(1, 4)];
        }
    }
    
    return $link;
}

// VARIAVEIS RESPONSAVEIS PELA TROCA DE DADOS ENTRE OS ARRAYS E SESSIONS O e X
function processarIA($link, $combinacoes) {
    // RESPONSAVEL PELA TROCA DO NUMERO DOS JOGADORES
    $_SESSION["jogador"] = ($_SESSION["jogador"] == 1) ? 2 : 1;

    // Esse bloco sera responsavel pela IA quando eu terminar minha escolha a tela vai da refresh e escolher um numero aleatorio a primeira vez na segunda
    // ele vai tentar pegar o mas adjacente ou viavel a logica vai ser a seguinte achar um simbolo que consiga andar duas casas em todas direções tendo
    // se a posição for possivel andar duas casas ele preench usando a propria logica de combinações
    if (primeiraSelecao($_SESSION["mascara"])) {
        // esse bloco é responsavel por carregar o a primeira jogada da IA
        header("location: core.php?link=" . sortearPrimeiraJogadaIa($_SESSION["mascara"], $link));
    
    } else {
        
        if ($_SESSION["jogador"] == 2) {
            // LOCALIZAR O ADVERSARIO
            $adversario = ($_SESSION["player"] == 'o') ? 'x' : 'o';

            // PROCURAR AS MARCAÇÕES DOS ADVERSARIOS
            foreach ($_SESSION["mascara"] as $chave => $mask) {
                if ($mask == $adversario) {
                    $chavesOcupadas[] = $chave;
                }
            }

            // LOCALIZAR UMA COMBINAÇÃO VALIDA
            $comb = null;
            foreach ($combinacoes as $combinacao) {
                $total = 0;
                foreach ($combinacao as $valida) {
                    foreach ($chavesOcupadas as $ocupada) {
                        if ($ocupada == $valida) {
                            ++ $total;
                            $comb = $combinacao;
                        }
                    }
                }

                if ($total == 2) {
                    break;
                }
            }

            $isFind = false;
            foreach ($_SESSION["mascara"] as $chave => $mask) {
                foreach ($comb as $val) {
                    if ($chave == $val && $mask == 2) {
                        $isFind = true;
                        header("location: core.php?link=" . $chave);
                    }
                }
            }

            if (! $isFind) {
                header("location: core.php?link=" . sortearPosicaoVazia($_SESSION["mascara"]));
            }
        }
    }
}

function trocarJogador($link){
    if ($_SESSION["player"] == 'o') {
        $_SESSION["mascara"][$link] = 'o';
        $_SESSION["player"] = 'x';
    } else {
        $_SESSION["mascara"][$link] = 'x';
        $_SESSION["player"] = 'o';
    }
}

//************************ BLOCO RESPONSAVEL PELO FLUXO CORRENTE *******************************************************
if (isset($_SESSION["jogador"])) {

    $link = $_GET["link"];
    $_SESSION["contador"] --;
    
    $combinacoes = array(
        array(0,1,2),
        array(3,4,5),
        array(6,7,8),
        array(0,3,6),
        array(1,4,7),
        array(2,5,8),
        array(0,4,8),
        array(2,4,6)
    );    
    
    trocarJogador($link);
    $fim = checarFinalJogo(validarJogadas($combinacoes));
    if($_SESSION["contador"] > 0 && !$fim){
        processarIA($link, $combinacoes);    
    }  
    
} else {
    
    // INSTACIA AS SESSIONS E VARIAVEIS
    $_SESSION["jogador"] = 1;
    $_SESSION["mascara"] = array();
    $_SESSION["contador"] = 9;
    $_SESSION["player"] = $_GET["player"];    
    $_SESSION["simbolo"] = $_GET["player"];
    
    // INICALIZA AS POSIÇOES COM VALOR NEUTRO
    for ($i = 0; $i <= 8; $i ++) {
        array_push($_SESSION["mascara"], 2);
    }

}
//************************ BLOCO RESPONSAVEL PELO FLUXO CORRENTE *******************************************************
?>
<body background="./imagens/caderno31.jpg">
	<table border="0" align="center">
		<tr>
			<td align="center">
        		<?php //RESPOSAVEL PAR EXIBIR A MENSAGEM QUE INDICA O JOGADOR NO MOMENTO ?>
        		<h3 style="color: #D20000">JOGADOR <b><?php echo $_SESSION["jogador"]; ?></b> VOC&#202; SELECIONOU <img src="./imagens/<?php echo $_SESSION["simbolo"]; ?>.png" width="25" height="25"></h3>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" align="center" background="./imagens/boasfotos_thirds.png"> 
                	<?php echo montarTabela($_SESSION["mascara"]); ?>                  
            	</table>
			</td>
		</tr>
		<tr align="center">
			<td colspan="3"><a href="index.php"><img src="./imagens/botao_voltar.png" border="none"></a></td>
		</tr>
	</table>
	<?php echo $_SESSION["som"]; ?>
</body>