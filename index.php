<?php
/**
 * 123PeopleRemover proxy
 *
 * @author Francois-Guillaume Ribreau
 *
 * @param $_GET['u']			url de la forme: http://www.123people.com/s/francois-guillaume+ribreau
 * @param $_GET['callback']		nom du callback JSONP
 */

include dirname(__FILE__).'/PeopleRemover.class.php';
include dirname(__FILE__).'/lib/TinyHttpClient.php';

$server = new PeopleRemover($_GET['url'], $_GET['callback']);
$server->run();
?>