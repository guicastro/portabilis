<?php

/*#######################################################
|														|
| Arquivo com a classe para criptografar ou descripto-	|
| grafar valores 										|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;

/*### FUNÇÃO QUE CRIPTOGRAFA OU DESCRIPTOGRAFA VALORES COM BASE EM UMA CHAVE ###*/
class CryptDecrypt {

	private $key;
	protected $DefuseKey;
	protected $value;

	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/
	public function __construct($container) {

		$this->key = \Defuse\Crypto\Key::loadFromAsciiSafeString($container['DefuseKey']);

		$this->DefuseKey = $container['DefuseKey'];
	}
	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/


	/*### CRIPTOGRAFA A INFORMAÇÃO, BASEADA NA CHAVE ###*/
	public function Crypt($value) {

		$this->value = \Defuse\Crypto\Crypto::Encrypt($value, $this->key);
		return $this->value;
	}
	/*### CRIPTOGRAFA A INFORMAÇÃO, BASEADA NA CHAVE ###*/


	/*### CRIPTOGRAFA A INFORMAÇÃO, BASEADA NA CHAVE ###*/
	public function Decrypt($value) {

		$this->value = \Defuse\Crypto\Crypto::Decrypt($value, $this->key);
		return $this->value;
	}
	/*### CRIPTOGRAFA A INFORMAÇÃO, BASEADA NA CHAVE ###*/


	/*### CRIPTOGRAFA A INFORMAÇÃO ONEWAY (MD5), BASEADA NA CHAVE ###*/
	public function CryptMD5($value) {

		$this->value = md5($value.$this->DefuseKey);
		return $this->value;
	}
	/*### CRIPTOGRAFA A INFORMAÇÃO ONEWAY (MD5), BASEADA NA CHAVE ###*/
}
/*### FUNÇÃO QUE CRIPTOGRAFA OU DESCRIPTOGRAFA VALORES COM BASE EM UMA CHAVE ###*/
