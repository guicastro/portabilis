<?php

/*#######################################################
|														|
| Arquivo com a classe que retorna os dados do Git de 	|
| Branch, Tag e Data 									|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;

/*### FUNÇÃO QUE RETORNA O ÚLTIMO BRANCH, TAG E DATA ###*/
class GitBranchTagDate {

	private $AbsolutePath;
	private $GetHead;
	private $branch;
	private $hash;
	private $tag;
	private $date;

	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/
	public function __construct($container) {

		//ARMAZENA O AbsolutePath
		$this->AbsolutePath = $container['AbsolutePath'];

		//OBTÉM DADOS DO HEAD
		$this->GetHead = trim(@file_get_contents($this->AbsolutePath. "/.git/HEAD"));

		/*### OBTÉM DADOS DA BRANCH ###*/
		if (substr($this->GetHead, 0, 4) == 'ref:') {

			$this->branch =  end(explode('/', $this->GetHead));
			$this->hash = trim(@file_get_contents($this->AbsolutePath . "/.git/refs/heads/{$this->branch}"));
		}
		/*### OBTÉM DADOS DA BRANCH ###*/


		/*### OBTÉM DADOS DAS TAGS ###*/
		// echo "<br>hash: ".$this->hash;
		$directoryIterator = new \DirectoryIterator($this->AbsolutePath."/.git/refs/tags");
		foreach($directoryIterator as $arquivo_tag)
			{
				// echo "<br>arquivo_tag: ".$arquivo_tag;
				// echo "<br>   conteudo arquivo_tag: ".file_get_contents($this->AbsolutePath."/.git/refs/tags/".$arquivo_tag);
				if(trim($this->hash)==trim(@file_get_contents($this->AbsolutePath."/.git/refs/tags/".$arquivo_tag)))
					{
						$this->tag = $arquivo_tag->__toString();
						$this->date = $arquivo_tag->getMTime();
					}
			}
		/*### OBTÉM DADOS DAS TAGS ###*/


		/*### OBTÉM DADOS DO HASH, SE NÃO TIVER UM TAG ###*/
		if($this->tag=="")
			{
				$this->tag = substr($this->hash,0,7);
				$this->date = filemtime($this->AbsolutePath . "/.git/refs/heads/{$this->branch}");
			}
		/*### OBTÉM DADOS DO HASH, SE NÃO TIVER UM TAG ###*/

	}
	/*### CONSTRUTOR, OBRIGATÓRIO DECLARAR O CONTAINER DO PIMPLE ###*/


	/*### RETORNA A BRANCH ###*/
	public function Branch() {

		return $this->branch;
	}
	/*### RETORNA A BRANCH ###*/


	/*### RETORNA O HASH ###*/
	public function Hash() {

		return $this->hash;
	}
	/*### RETORNA O HASH ###*/


	/*### RETORNA A TAG ###*/
	public function Tag() {

		return $this->tag;
	}
	/*### RETORNA A TAG ###*/


	/*### RETORNA A DATA DA ÚLTIMA ALTERAÇÃO ###*/
	public function Date() {

		return $this->date;
	}
	/*### RETORNA A DATA DA ÚLTIMA ALTERAÇÃO ###*/

}
/*### FUNÇÃO QUE RETORNA O ÚLTIMO BRANCH, TAG E DATA ###*/
