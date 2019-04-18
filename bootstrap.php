<?php

use App\Commands\ImportAllCommercialAgentsCommand;
use GuzzleHttp\Client;
use App\Adapter\GuzzleHttpAdapter;
use App\AgentImporter\AgentMapper;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$app = new Silly\Edition\Pimple\Application();
$container = $app->getContainer();
$container[PDO::class] = function($c){

	$host = getenv("DB_HOST");
	$db = getenv("DB_NAME");
	$user = getenv("DB_USER");
	$pass = getenv("DB_PASSWORD");
	$charset = getenv("DB_CHARSET");

	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false
	];
	try{
		$pdo = new PDO($dsn, $user, $pass, $options);
		return $pdo;
	}catch(PDOException $e){
		throw new PDOException($e->getMessage(), (int)$e->getCode());
	}
};

$container[AgentMapper::class] = function($c){
	return new AgentMapper($c->get(PDO::class));
};

$container[Client::class] = function($c){
	$token = getenv('COOPER_API_TOKEN');
	$client = new Client(
		[
		'base_uri' => 'http://api.cooperandco.co.nz/',
		'headers' => ['Authorization' => sprintf('Bearer %s', $token)]
		]
	);
	return $client;
};

$container[GuzzleHttpAdapter::class] = function($c){
	return new GuzzleHttpAdapter($c->get(Client::class));
};

$container[ImportAllCommercialAgentsCommand::class] = function($c){
	return new ImportAllCommercialAgentsCommand($c->get(GuzzleHttpAdapter::class), $c->get(AgentMapper::class), $c->get(Logger::class));
};

$container[Logger::class] = function(){
	// create a log channel
	$logger = new Logger('name');

	//Default to Info level Logging
	$logger->pushHandler(new StreamHandler(__DIR__ . '/app.log', Logger::INFO));
	return $logger;
};

//Commands
$app->command('agents [name]', [ImportAllCommercialAgentsCommand::class, 'execute']);

$app->command('agentsdrop', [ImportAllCommercialAgentsCommand::class, 'dropTable']);