<?php

namespace App\Commands;
use Symfony\Component\Console\Output\OutputInterface;
use App\Adapter\HttpClientAdapterInterface;
use App\Exceptions\HttpException;
use App\AgentImporter\AgentMapper;
use App\AgentImporter\Agent;
use Monolog\Logger;

class ImportAllCommercialAgentsCommand
{
	private $httpAdapter;
	private $agentMapper;
    private $logger;

    public function __construct(HttpClientAdapterInterface $httpAdapter, AgentMapper $agentMapper, Logger $logger)
    {
        $this->httpAdapter = $httpAdapter;
        $this->agentMapper = $agentMapper;
        $this->logger = $logger;
    }

    public function execute($name, OutputInterface $output)
    {	$this->logger->info('Commercial Agent Import Started');
        try{
            $body = $this->httpAdapter->get('users/search/?pageSize=1000');
            }catch(HttpException $he){
            $this->logger->error('Error While trying to Import Agent : Message : ' . $he->getMessage() . 'Code : ' . $he->getCode());
            $body = false;
        }
        if($body){
            $this->sync($body);
        }
    }

    public function sync($body)
    {
        $agents = json_decode($body);
        foreach ($agents as $key => $agent) {
            if($agent->securityGroup === 'SalesConsultant' && $agent->isCommercial === true){
                $data['nai_id'] = $agent->id;
                $data['name'] = $agent->firstName . ' ' . $agent->lastName;
                $data['firstName'] = $agent->firstName;
                $data['lastName'] = $agent->lastName;
                $data['defaultPhotoURL'] = $agent->pictureUrl;
                $data['emailAddress'] = $agent->email;
                $data['businessPhone'] = $agent->phone;
                $data['mobilePhone'] = $agent->mobile;
                $data['specialty'] = $agent->position;
    
                $agentObject = new Agent($data);
                $agentInserted = $this->agentMapper->insertIfNotExist($agentObject);
                if($agentInserted){
                    $this->logger->info('Importing Commercial Agent with Nai_Id ' . $agentInserted->getNaiId() . ' and name : ' . $agentInserted->getFullName());
                }else{
                    $this->logger->info('Skipping because already exist Commercial Agent with Nai_id ' .$data['nai_id'] . ' and name : ' . $data['name']);
                }
            }
        }
    }

    public function dropTable()
    {
        $this->agentMapper->drop();
    }


}