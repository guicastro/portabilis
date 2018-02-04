<?php

/*#######################################################
|														|
| Arquivo com a classe para tratamento de variáveis		|
| para prevenir SQL Injection							|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;


/*### CLASSE PARA PREVENIR SQL INJECTION ###*/
class AntiInjection {

	protected $value;

	/*### CONSTRUTOR ###*/
	public function __construct() {

	}
	/*### CONSTRUTOR ###*/


	/*### EXECUTA O TRATAMENTO, RETORNANDO VARIÁVEL LIVRE DE SQL INJECTION ###*/
	public function Prepare($value) {

		$result = preg_replace_callback(
		    "/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i",
		    function($m) { return CallFunction($m); },
		    $value
		);

		// echo "---";
		// print_r($result);
		// echo "---";

  		/*$value = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/ie","",$value);
  		$value = trim($value);
		$value = strip_tags($value);
		$value = addslashes($value);*/

		$this->value = $result;
		return $this->value;
	}
	/*### EXECUTA O TRATAMENTO, RETORNANDO VARIÁVEL LIVRE DE SQL INJECTION ###*/
}
/*### CLASSE PARA PREVENIR SQL INJECTION ###*/