<?php

/*#######################################################
|														|
| Arquivo com a classe que retorna os dados do Ambiente |
| em que a aplicação está rodando 						|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;

/*### FUNÇÃO QUE RETORNA O AMBIENTE DA APLICAÇÃO ###*/
class AppEnvironment {

	private $HttpReferer;
	private $Environment;

	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/
	public function __construct($container) {

		$this->HttpReferer = $container['HttpReferer'];

		if($this->HttpReferer=="multiacao.testesite.com.br") $this->Environment = "HML";
		else $this->Environment = $container["ServerName"]." [".$this->HttpReferer."]";
	}
	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/


	/*### RETORNA O AMBIENTE ###*/
	public function Environment() {

		return $this->Environment;
	}
	/*### RETORNA O AMBIENTE ###*/


}
/*### FUNÇÃO QUE RETORNA O AMBIENTE DA APLICAÇÃO ###*/
