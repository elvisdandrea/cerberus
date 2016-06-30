<?php

/**
 * Class boletoitau
 *
 * Under edition ...
 *
 * @author  Eder D'Andrea
 */
class boletoitau{

    private $codigobanco = "341";
    private $nummoeda = "9";

    public $is_render_pdf = false;
    public $identificacao;
    public $valor_cobrado; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
    public $nosso_numero;  // Nosso numero - REGRA: Máximo de 8 caracteres!
    public $numero_documento;	// Num do pedido ou nosso numero
    public $data_vencimento; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA

    // DADOS DO SEU CLIENTE
    public $pagador;
    public $cpf_pagador;
    public $endereco1;
    public $endereco2;

    // INFORMACOES PARA O CLIENTE
    public $instrucoes;

    // DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
    public $quantidade;
    public $valor_unitario;
    public $aceite;
    public $especie = "R$";
    public $especie_doc;
    // ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //
    // DADOS DA SUA CONTA - ITAÚ
    public $agencia = "0579"; // Num da agencia, sem digito
    public $conta = "44229";	// Num da conta, sem digito
    public $conta_dv = "4"; 	// Digito do Num da conta
    // DADOS PERSONALIZADOS - ITAÚ
    public $carteira = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

    // SEUS DADOS
    public $cpf_cnpj = "09.083.397/0001-05";
    public $endereco = "R. Alcebíades Antônio dos Santos, 355 - Nonoai";
    public $cidade_uf = "Cidade / Estado";
    public $cedente = "Soleil tecnologia Imp. e Exp. Ltda";

    private $codigo_barras;
    private $agencia_codigo;
    private $linha_digitavel;
    private $codigo_banco_com_dv;
    private $barCode;

    public function render(){
        $codigo_banco_com_dv = $this->geraCodigoBanco($this->codigobanco);

        $fator_vencimento = $this->fator_vencimento($this->data_vencimento);
        $this->valor_cobrado = str_pad($this->valor_cobrado, 10, '0', STR_PAD_LEFT);
        $codigo_barras = $this->codigobanco . $this->nummoeda . $fator_vencimento . $this->valor_cobrado . $this->carteira . $this->nosso_numero . $this->modulo_10($this->agencia . $this->conta . $this->carteira . $this->nosso_numero) . $this->agencia . $this->conta . $this->modulo_10($this->agencia . $this->conta).'000';

        // 43 numeros para o calculo do digito verificador
        $dv = $this->getDigitoVerificadorBarra($codigo_barras);

        // Numero para o codigo de barras com 44 digitos
        $codigo_barras = $linha = substr($codigo_barras, 0, 4). $dv .substr($codigo_barras, 4, 43);

        $nossonumero = $this->carteira . '/' . $this->nosso_numero. '-' . $this->modulo_10($this->agencia . $this->conta . $this->carteira . $this->nosso_numero);
        $agencia_codigo = $this->agencia . " / " . $this->conta ."-". $this->modulo_10($this->agencia . $this->conta);

        $this->codigo_barras = $linha;
        $this->linha_digitavel = $this->montaLinhaDigitavel($linha); // verificar
        $this->agencia_codigo = $agencia_codigo ;
        $this->nosso_numero = $nossonumero;
        $this->codigo_banco_com_dv = $codigo_banco_com_dv;
        $this->barCode = $this->generateBarcde($codigo_barras);

        ob_start();
        require_once('layout.php');
        $result = ob_get_clean();
        return $result;
    }

    public function setAttribute($name, $value){
        $this->$name = $value;
        return $this;
    }

    private function getDigitoVerificadorBarra($numero) {
        $resto2 = $this->modulo_11($numero, 9, 1);
        $digito = 11 - $resto2;
        if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
            $dv = 1;
        } else {
            $dv = $digito;
        }
        return $dv;
    }

    private function generateBarcde($valor){
        $valor = str_replace(array('.', ' '), '', $valor);

        $fino = 'thin' ;
        $largo = 'wide' ;
        $barcodes[0] = "00110" ;
        $barcodes[1] = "10001" ;
        $barcodes[2] = "01001" ;
        $barcodes[3] = "11000" ;
        $barcodes[4] = "00101" ;
        $barcodes[5] = "10100" ;
        $barcodes[6] = "01100" ;
        $barcodes[7] = "00011" ;
        $barcodes[8] = "10010" ;
        $barcodes[9] = "01010" ;

        for($f1=9;$f1>=0;$f1--){
            for($f2=9;$f2>=0;$f2--){
                $f = ($f1 * 10) + $f2 ;
                $texto = "" ;
                for($i=1;$i<6;$i++){
                    $texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
                }
                $barcodes[$f] = $texto;
            }
        }
        $barcode = "
        <span class='black $fino'></span>
        <span class='white $fino'></span>
        <span class='black $fino'></span>
        <span class='white $fino'></span>";

        $texto = $valor ;

        if((strlen($texto) % 2) <> 0){
            $texto = "0" . $texto;
        }

        // Draw dos dados
        while (strlen($texto) > 0) {
            $i = round(substr($texto,0,2));

            $texto = substr($texto,strlen($texto)-(strlen($texto)-2),strlen($texto)-2);

            $f = $barcodes[$i];
            for($i=1; $i<11; $i+=2){
                if (substr($f,($i-1),1) == "0") {
                    $f1 = $fino;
                }else{
                    $f1 = $largo;
                }

                $barcode .= "<span class='black $f1'></span>";

                if (substr($f,$i,1) == "0") {
                    $f2 = $fino ;
                }else{
                    $f2 = $largo ;
                }
                $barcode .= "<span class='white $f2'></span>";
            }
        }

        // Draw guarda final
        $barcode .= "<span class='black $largo'></span>
        <span class='white $fino'></span>
        <span class='black $fino'></span>";

        return $barcode;
    }

    function fator_vencimento($data) {
        $data = explode("/",$data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
    }

    private function _dateToDays($year, $month, $day) {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }
        return ( floor((  146097 * $century)    /  4 ) +
            floor(( 1461 * $year)        /  4 ) +
            floor(( 153 * $month +  2) /  5 ) +
            $day +  1721119);
    }

    private function modulo_10($num) {
        $numtotal10 = 0;
        $fator = 2;
        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator;
            $temp0=0;
            foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){ $temp0+=$v; }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }

        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;

    }

    /**
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador
     *    de boletos bancarios conforme documentos obtidos
     *    da Febraban - www.febraban.org.br
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */
    private function modulo_11($num, $base=9, $r=0)  {

        $soma = 0;
        $fator = 2;
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }


    private function montaLinhaDigitavel($codigo) {
        // campo 1
        $banco    = substr($codigo,0,3);
        $moeda    = substr($codigo,3,1);
        $ccc      = substr($codigo,19,3);
        $ddnnum   = substr($codigo,22,2);
        $dv1      = $this->modulo_10($banco.$moeda.$ccc.$ddnnum);
        // campo 2
        $resnnum  = substr($codigo,24,6);
        $dac1     = substr($codigo,30,1);//modulo_10($agencia.$conta.$carteira.$nnum);
        $dddag    = substr($codigo,31,3);
        $dv2      = $this->modulo_10($resnnum.$dac1.$dddag);
        // campo 3
        $resag    = substr($codigo,34,1);
        $contadac = substr($codigo,35,6); //substr($codigo,35,5).modulo_10(substr($codigo,35,5));
        $zeros    = substr($codigo,41,3);
        $dv3      = $this->modulo_10($resag.$contadac.$zeros);
        // campo 4
        $dv4      = substr($codigo,4,1);
        // campo 5
        $fator    = substr($codigo,5,4);
        $valor    = substr($codigo,9,10);

        $campo1 = substr($banco.$moeda.$ccc.$ddnnum.$dv1,0,5) . '.' . substr($banco.$moeda.$ccc.$ddnnum.$dv1,5,5);
        $campo2 = substr($resnnum.$dac1.$dddag.$dv2,0,5) . '.' . substr($resnnum.$dac1.$dddag.$dv2,5,6);
        $campo3 = substr($resag.$contadac.$zeros.$dv3,0,5) . '.' . substr($resag.$contadac.$zeros.$dv3,5,6);
        $campo4 = $dv4;
        $campo5 = $fator.$valor;

        return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }

    private function geraCodigoBanco($numero) {
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->modulo_11($parte1);
        return $parte1 . "-" . $parte2;
    }
}