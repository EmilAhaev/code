<?

$application = \Bitrix\Main\Application::getInstance();
$context = $application->getContext();
$request = $context->getRequest();
$Response = $context->getResponse();
$Server = $context->getServer();
$server_get = $Server->toArray();

$_SERVER["REQUEST_URI"] = /урл страницы настроящий, контент которой должен отобразиться/;
$server_get["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
$Server->set($server_get);
$context->initialize(new Bitrix\Main\HttpRequest($Server, array(), array(), array(), $_COOKIE), $Response, $Server);
