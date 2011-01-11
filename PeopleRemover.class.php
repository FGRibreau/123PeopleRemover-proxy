<?php
/**
 * 123PeopleRemover class
 * 
 * @TODO Ajouter des commentaires
 * @TODO Test-unitaire
 * @TODO Sortir le "printOut" & "printOurError" ...
 * @author Francois-Guillaume Ribreau
 *
 */
class PeopleRemover{
	
	private $url, $callback, $httpClient, $htmlSource
			, $VERSION, $PROD, $SERVER123PEOPLE;
	
	function __construct($http_get_url = null, $http_get_callback){
		//Old static var
		$this->VERSION = 1;
		$this->PROD = true;
		$this->SERVER123PEOPLE = 'www.123people.fr';
		
		$this->url = $http_get_url;
		$this->callback = $http_get_callback;
		$httpClient = null;
		$htmlSource = null;
	}
	
	public function run(){
		
		if(empty($this->callback)){
			$this->callback = 'defaultCallback';
			echo $this->printOutError(0, 'No parameter "callback" specified');
			return;
		}
		
		if(empty($this->url)){
			echo $this->printOutError(1, 'No parameter "url" specified');
			return;
		}
		
		if(!$this->isGoodUrl()){
			echo $this->printOutError(2, 'Bad url value');
			return;
		}
		
		return $this->httpClientInit() && $this->httpClientGet() && $this->httpClientParse();
	}
	
	/**
	 * isGoodUrl
	 * Test si $http_get_url est valide
	 */
	public function isGoodUrl(){
		return strpos($this->url, 'http://www.123people.') == 0 && strpos($this->url, '/s/') != false;
	}
	
	public function httpClientInit(){
		$this->httpClient = new TinyHttpClient();  
	    $this->httpClient->debug = false;
		return true;
	}
	
	/**
	 * httpClientGet
	 * Récupère le code html de la page
	 **/
	public function httpClientGet(){
		if($this->PROD){
			$this->htmlSource = $this->httpClient->getRemoteFile($this->SERVER123PEOPLE, 80, $this->getRequestUrl(), '', 4096, 'get');
		} else {
			$this->htmlSource = file_get_contents('tmp.html');
		}

		if(empty($this->htmlSource)){
			echo $this->printOutError(3, 'The server returned an empty source');
			return false;
		}
		
		return true;
	}
	
	public function getRequestUrl(){
		return '/s'.substr($this->url, strripos($this->url,'/'));
	}
	
	/**
	 * httpClientParse
	 * Ici se trouve la partie... "sale"
	 * Je suis ouvert à toute participation !
	 */
	public function httpClientParse(){
		//Get all links in $links[]
		if(!preg_match_all("(((f|ht){1}tp://)[-a-zA-Z0-9@:%_;\+.|~#?&//=]+)", $this->htmlSource, $links) 
		|| count($links) == 0
		|| count($links[0]) == 0){
			echo $this->printOutError(4, 'No links');
			return false;
		}

		function isBadLink($v){
			return stripos($v, '123people') === FALSE 
			&& stripos($v, 'commander.1and1.fr') === FALSE
			&& stripos($v, 'w3.org/TR') === FALSE
			&& stripos($v, 'yahooapis.com') === FALSE
			&& stripos($v, 'w3.org/1999') === FALSE 
			&& substr_count($v,'.') >= 2;
		}
		
		//Quick & dirty -__-'
		$links = array_values(array_unique( array_filter($links[0], 'isBadLink')));

		if(count($links) == 0){
			echo printOutError(5, 'There are no links :s');
			return false;
		}
		
		echo $this->printOut(array('links' => $links));
		return true;
	}

	/**
	 * printOut
	 * Retourne un JSONP
	 */
	private function printOut($obj){
		header('Content-type:application/json');
		return $this->callback.'('.json_encode(array('version' => $this->VERSION, 'content' => $obj)).');';
	}
	
	/**
	 * printOutError
	 * Retourne un JSONP d'erreur
	 */
	private function printOutError($id, $message){
		return $this->printOut(array('errId' => $id, 'errMsg' => $message));
	}
}


?>