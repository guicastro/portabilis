<?php

/*#######################################################
|														|
| Arquivo com a classe padrão de conexão com o banco	|
| de dados usando PDO
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/


namespace Database;


/*### CONEXÃO COM O BANCO DE DADOS ##*/
class Connect implements IConnect {

	private $driver;
	private $host;
	private $dbname;
	private $charset;
	private $user;
	private $pass;

	/*### OBRIGATÓRIO AS VARIÁVEIS DE HOST, USER E PASS ##*/
	public function __construct($driver,$host,$dbname,$charset,$user,$pass) {

		$this->driver = $driver;
		$this->host = $host;
		$this->dbname = $dbname;
		$this->charset = $charset;
		$this->user = $user;
		$this->pass = $pass;
	}
	/*### OBRIGATÓRIO AS VARIÁVEIS DE HOST, USER E PASS ##*/


	/*### MÉTODO DE CONEXÃO ##*/
	public function Connect() {

		//CONECTA USANDO PDO
	    $PDO = new \PDO($this->driver.":host=".$this->host.";dbname=".$this->dbname,$this->user,$this->pass);

		//MODIFICA O CONTROLE DE ERROS DO PDO PARA ERRMODE_EXCEPTION
		$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$PDO->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
		$PDO->setAttribute(\PDO::MYSQL_ATTR_DIRECT_QUERY, true);

	    //RETORNA O OBJETO DO PDO
	    return $PDO;
	}
	/*### MÉTODO DE CONEXÃO ##*/

}
/*### CONEXÃO COM O BANCO DE DADOS ##*/
