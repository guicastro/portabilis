<?php

/*#######################################################
|														|
| Arquivo com a classe formata os dados e valores em 	|
| todas as outras classes 								|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;

/*### FUNÇÃO QUE APLICA MÁSCARA DE DADOS E VALORES ###*/
class MaskValue {

	private $value;

	/*### CONSTRUTOR ###*/
	public function __construct() {



		/*#### FUNÇÕES PARA LIMPEZA DO NOME DO ARQUIVO (baseado em  Germanix / Wordpress) ####*/
		/**
		 * Limpar nome de arquivo no upload
		 *
		 * Sanitization test done with the filename:
		 * ÄäÆæÀàÁáÂâÃãÅåªₐāĆćÇçÐđÈèÉéÊêËëₑƒğĞÌìÍíÎîÏïīıÑñⁿÒòÓóÔôÕõØøₒÖöŒœßŠšşŞ™ÙùÚúÛûÜüÝýÿŽž¢€‰№$℃°C℉°F⁰¹²³⁴⁵⁶⁷⁸⁹₀₁₂₃₄₅₆₇₈₉±×₊₌⁼⁻₋–—‑․‥…‧.png
		 * @author toscho
		 * @url    https://github.com/toscho/Germanix-WordPress-Plugin
		 */
		function t5f_sanitize_filename( $filename ) {

		    $filename    = html_entity_decode( $filename, ENT_QUOTES, 'utf-8' );
		    $filename    = t5f_translit( $filename );
		    $filename    = t5f_lower_ascii( $filename );
		    $filename    = t5f_remove_doubles( $filename );
		    return $filename;
		}

		/**
		 * Converte maiúsculas em minúsculas e remove o resto.
		 * https://github.com/toscho/Germanix-WordPress-Plugin
		 *
		 * @uses   apply_filters( 'germanix_lower_ascii_regex' )
		 * @param  string $str Input string
		 * @return string
		 */
		function t5f_lower_ascii( $str ) {
		    $str     = strtolower( $str );
		    $regex   = array(
		        'pattern'        => '~([^a-z\d_.-])~'
		        , 'replacement'  => ''
		    );
		    // Leave underscores, otherwise the taxonomy tag cloud in the
		    // backend won’t work anymore.
		    return preg_replace( $regex['pattern'], $regex['replacement'], $str );
		}


		/**
		 * Reduz meta caracteres (-=+.) repetidos para apenas um.
		 * https://github.com/toscho/Germanix-WordPress-Plugin
		 *
		 * @param  string $str Input string
		 * @return string
		 */
		function t5f_remove_doubles( $str ) {
		    $regex = array(
		        'pattern'        => '~([=+.-])\\1+~'
		        , 'replacement'  => "\\1"
		    );
		    return preg_replace( $regex['pattern'], $regex['replacement'], $str );
		}


		/**
		 * Substitui caracteres não-ASCII.
		 * https://github.com/toscho/Germanix-WordPress-Plugin
		 *
		 * Modified version of Heiko Rabe’s code.
		 *
		 * @author Heiko Rabe http://code-styling.de
		 * @link   http://www.code-styling.de/?p=574
		 * @param  string $str
		 * @return string
		 */
		function t5f_translit( $str ) {
		    $utf8 = array(
		        'Ä'  => 'Ae'
		        , 'ä'    => 'ae'
		        , 'Æ'    => 'Ae'
		        , 'æ'    => 'ae'
		        , 'À'    => 'A'
		        , 'à'    => 'a'
		        , 'Á'    => 'A'
		        , 'á'    => 'a'
		        , 'Â'    => 'A'
		        , 'â'    => 'a'
		        , 'Ã'    => 'A'
		        , 'ã'    => 'a'
		        , 'Å'    => 'A'
		        , 'å'    => 'a'
		        , 'ª'    => 'a'
		        , 'ₐ'    => 'a'
		        , 'ā'    => 'a'
		        , 'Ć'    => 'C'
		        , 'ć'    => 'c'
		        , 'Ç'    => 'C'
		        , 'ç'    => 'c'
		        , 'Ð'    => 'D'
		        , 'đ'    => 'd'
		        , 'È'    => 'E'
		        , 'è'    => 'e'
		        , 'É'    => 'E'
		        , 'é'    => 'e'
		        , 'Ê'    => 'E'
		        , 'ê'    => 'e'
		        , 'Ë'    => 'E'
		        , 'ë'    => 'e'
		        , 'ₑ'    => 'e'
		        , 'ƒ'    => 'f'
		        , 'ğ'    => 'g'
		        , 'Ğ'    => 'G'
		        , 'Ì'    => 'I'
		        , 'ì'    => 'i'
		        , 'Í'    => 'I'
		        , 'í'    => 'i'
		        , 'Î'    => 'I'
		        , 'î'    => 'i'
		        , 'Ï'    => 'Ii'
		        , 'ï'    => 'ii'
		        , 'ī'    => 'i'
		        , 'ı'    => 'i'
		        , 'I'    => 'I' // turkish, correct?
		        , 'Ñ'    => 'N'
		        , 'ñ'    => 'n'
		        , 'ⁿ'    => 'n'
		        , 'Ò'    => 'O'
		        , 'ò'    => 'o'
		        , 'Ó'    => 'O'
		        , 'ó'    => 'o'
		        , 'Ô'    => 'O'
		        , 'ô'    => 'o'
		        , 'Õ'    => 'O'
		        , 'õ'    => 'o'
		        , 'Ø'    => 'O'
		        , 'ø'    => 'o'
		        , 'ₒ'    => 'o'
		        , 'Ö'    => 'Oe'
		        , 'ö'    => 'oe'
		        , 'Œ'    => 'Oe'
		        , 'œ'    => 'oe'
		        , 'ß'    => 'ss'
		        , 'Š'    => 'S'
		        , 'š'    => 's'
		        , 'ş'    => 's'
		        , 'Ş'    => 'S'
		        , '™'    => 'TM'
		        , 'Ù'    => 'U'
		        , 'ù'    => 'u'
		        , 'Ú'    => 'U'
		        , 'ú'    => 'u'
		        , 'Û'    => 'U'
		        , 'û'    => 'u'
		        , 'Ü'    => 'Ue'
		        , 'ü'    => 'ue'
		        , 'Ý'    => 'Y'
		        , 'ý'    => 'y'
		        , 'ÿ'    => 'y'
		        , 'Ž'    => 'Z'
		        , 'ž'    => 'z'
		        // misc
		        , '¢'    => 'Cent'
		        , '€'    => 'Euro'
		        , '‰'    => 'promille'
		        , '№'    => 'Nr'
		        , '$'    => 'Dollar'
		        , '℃'    => 'Grad Celsius'
		        , '°C' => 'Grad Celsius'
		        , '℉'    => 'Grad Fahrenheit'
		        , '°F' => 'Grad Fahrenheit'
		        // Superscripts
		        , '⁰'    => '0'
		        , '¹'    => '1'
		        , '²'    => '2'
		        , '³'    => '3'
		        , '⁴'    => '4'
		        , '⁵'    => '5'
		        , '⁶'    => '6'
		        , '⁷'    => '7'
		        , '⁸'    => '8'
		        , '⁹'    => '9'
		        // Subscripts
		        , '₀'    => '0'
		        , '₁'    => '1'
		        , '₂'    => '2'
		        , '₃'    => '3'
		        , '₄'    => '4'
		        , '₅'    => '5'
		        , '₆'    => '6'
		        , '₇'    => '7'
		        , '₈'    => '8'
		        , '₉'    => '9'
		        // Operators, punctuation
		        , '±'    => 'plusminus'
		        , '×'    => 'x'
		        , '₊'    => 'plus'
		        , '₌'    => '='
		        , '⁼'    => '='
		        , '⁻'    => '-' // sup minus
		        , '₋'    => '-' // sub minus
		        , '–'    => '-' // ndash
		        , '—'    => '-' // mdash
		        , '‑'    => '-' // non breaking hyphen
		        , '․'    => '.' // one dot leader
		        , '‥'    => '..'  // two dot leader
		        , '…'    => '...'  // ellipsis
		        , '‧'    => '.' // hyphenation point
		        , ' '    => '-'   // nobreak space
		        , ' '    => '-'   // normal space
		    );

		    $str = strtr( $str, $utf8 );
		    return trim( $str, '-' );
		}
		/*#### FUNÇÕES PARA LIMPEZA DO NOME DO ARQUIVO (baseado em  Germanix / Wordpress) ####*/


	}
	/*### CONSTRUTOR ###*/


	/*### MÁSCARA DE CPF ###*/
	public function Cpf($value, $action = 'add') {

		switch($action) {

			case 'add':
				//ADICIONA OS PONTOS E TRAÇOS
				$this->value = ($value<>"") ? substr($value,0,3).".".substr($value,3,3).".".substr($value,6,3)."-".substr($value,9,2) : "";
				break;
			case 'remove':
				//REMOVE OS PONTOS E TRAÇOS
				$this->value = str_replace(".","",str_replace("-","",$value));
				break;
		}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE CPF ###*/




	/*### MÁSCARA DE CNPJ ###*/
	public function Cnpj($value, $action = 'add') {

		switch($action) {

			case 'add':
				//ADICIONA OS PONTOS E TRAÇOS
				$this->value = ($value<>"") ? substr($value,0,2).".".substr($value,2,3).".".substr($value,5,3)."/".substr($value,8,4)."-".substr($value,12,2) : "";
				break;
			case 'remove':
				//REMOVE OS PONTOS E TRAÇOS
				$this->value = str_replace(".","",str_replace("-","",str_replace("/","",$value)));
				break;
		}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE CNPJ ###*/






	/*### MÁSCARA DE CPF E CNPJ, BASEADO NO TAMANHO DA STRING ###*/
	public function CpfCnpj($value, $action = 'add') {

		$Length = strlen(str_replace(".","",str_replace("-","",str_replace("/","",$value))));

		if(($Length==11)&&($action=="add")) {

			//ADICIONA OS PONTOS E TRAÇOS
			$this->value = ($value<>"") ? substr($value,0,3).".".substr($value,3,3).".".substr($value,6,3)."-".substr($value,9,2) : "";
		}
		elseif(($Length==14)&&($action=="add")) {

			//ADICIONA OS PONTOS, TRAÇOS E A BARRA
			$this->value = ($value<>"") ? substr($value,0,2).".".substr($value,2,3).".".substr($value,5,3)."/".substr($value,8,4)."-".substr($value,12,2) : "";
		}
		else {

			//REMOVE OS PONTOS, TRAÇOS E A BARRA
			$this->value = str_replace(".","",str_replace("-","",str_replace("/","",$value)));
		}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE CPF E CNPJ, BASEADO NO TAMANHO DA STRING ###*/






	/*### CONVERSÃO DE DATA ###*/
	public function Data($value, $action = 'US2BR') {

		$OnlyDate = substr($value,0,10);
		$OnlyTime = substr($value,10,6);

		if(($value<>"")&&($value<>"0000-00-00 00:00:00")&&($value<>"0000-00-00"))
			{
				switch($action) {

					case 'US2BR':
						//CONVERTE DE YYYY-MM-DD PARA DD/MM/YYYY
						$Date = \DateTime::createFromFormat('Y-m-d', $OnlyDate);
						$this->value = $Date->format('d/m/Y');
						break;
					case 'US2BR_TIME':
						//CONVERTE DE YYYY-MM-DD PARA DD/MM/YYYY
						$Date = \DateTime::createFromFormat('Y-m-d', $OnlyDate);
						$this->value = $Date->format('d/m/Y')." ".$OnlyTime;
						break;
					case 'BR2US':
						//CONVERTE DE DD/MM/YYYY PARA YYYY-MM-DD
						$Date = \DateTime::createFromFormat('d/m/Y', $OnlyDate);
						$this->value = $Date->format('Y-m-d');
						break;
				}
			}
		else
			{
				$this->value = NULL;
			}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### CONVERSÃO DE DATA ###*/



	/*### MÁSCARA DE TELEFONE ###*/
	public function Telefone($value, $action = 'add') {

		//REMOVE TODOS OS ESPAÇOS, TRAÇOS E PARÊNTESES
		$ClearedValue = str_replace("(","",str_replace(")","",str_replace("-","",str_replace(" ","",$value))));

		//ARMAZENA O NÚMERO DE CARACTERES DO VALOR
		$Length = strlen($ClearedValue);


		/*### SE O TELEFONE FOR DE 11 DÍGITOS (COM 9º DÍGITO) E A ACTION FOR ADD ###*/
		if(($Length==11)&&($action=="add"))
			{
				$this->value = "(".substr($value,0,2).") ".substr($value,2,5)."-".substr($value,7,4);
			}
		/*### SE O TELEFONE FOR DE 11 DÍGITOS (COM 9º DÍGITO) E A ACTION FOR ADD ###*/


		/*### SE O TELEFONE FOR DE 10 DÍGITOS E A ACTION FOR ADD ###*/
		else if(($Length==10)&&($action=="add"))
			{
				$this->value = "(".substr($value,0,2).") ".substr($value,2,4)."-".substr($value,6,4);
			}
		/*### SE O TELEFONE FOR DE 10 DÍGITOS E A ACTION FOR ADD ###*/


		/*### SENÃO, RETORNA O VALOR LIMPO ###*/
		else
			{
				$this->value = $ClearedValue;
			}
		/*### SENÃO, RETORNA O VALOR LIMPO ###*/

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE TELEFONE ###*/



	/*### MÁSCARA DE CEP ###*/
	public function Cep($value, $action = 'add') {

		switch($action) {

			case 'add':
				//ADICIONA O TRAÇO SEPARADOR DO CEP
				$this->value = ($value<>"") ? substr($value,0,5)."-".substr($value,5,3) : "";
				break;
			case 'remove':
				//REMOVE O TRAÇO SEPARADOR DO CEP
				$this->value = str_replace("-","",$value);
				break;
		}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE CEP ###*/





	/*### MÁSCARA DE CEP ###*/
	public function Moeda($value, $action = 'add') {

		switch($action) {

			case 'add':
				//FORMATA O NÚMERO
				$this->value = ($value<>"") ? number_format($value,2,",",".") : "";
				break;
			case 'remove':
				//REMOVE O TRAÇO SEPARADOR DO CEP
				$this->value = str_replace(",",".",str_replace(".","",$value));
				break;
		}

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA DE CEP ###*/





	/*### MÁSCARA PARA SUBSTRING DO TEXTO DIGITADO ###*/
	public function Substr($value, $lenght = 1) {

		//RESTRINGE AO TAMANHO ESCOLHIDO
		$this->value = substr($value, 0, $lenght);

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA PARA SUBSTRING DO TEXTO DIGITADO ###*/





	/*### MÁSCARA PARA TRATAR VALOR NULO COM TEXTO ESPECÍFICO ###*/
	public function Null($value, $text) {


		//SUBSTITUI O VALOR NULO PELO TEXTO ESPECIFICADO NA ACTION
		$this->value = ($value=="") ? $text : $value;

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA PARA TRATAR VALOR NULO COM TEXTO ESPECÍFICO ###*/





	/*### MÁSCARA PARA REMOVER CARACTERES ESTRANHOS PARA NOMES DE ARQUIVO ###*/
	public function Filename($value, $action = 'add') {

		//APLICA FUNÇÃO PARA TRATAR O NOME
		$this->value = t5f_sanitize_filename($value);

		//RETORNA O VALOR FORMATADO
		return $this->value;
	}
	/*### MÁSCARA PARA REMOVER CARACTERES ESTRANHOS PARA NOMES DE ARQUIVO ###*/





}
/*### FUNÇÃO QUE APLICA MÁSCARA DE DADOS E VALORES ###*/
