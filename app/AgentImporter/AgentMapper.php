<?php

namespace App\AgentImporter;
use PDO;

class AgentMapper
{
	private $db;

	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Fetch All Agents
	 * @return Agents[]
	 */
	public function fetchAll()
	{
		$sql = 'SELECT * FROM agent';
		$stmt = $this->db->query($sql);

		$results = [];
		while ($row = $stmt->fetch()) {
			$results[] = new Agent($row);
		}

		return $results;
	}

	/**
	 * Get a single Agent 
	 * @param  int $nai_id 
	 * @return Agent | false
	 */
	public function getByNaiId($nai_id)
	{
		$sql = 'SELECT * FROM agent WHERE nai_id = :nai_id';
		$stmt = $this->db->prepare($sql);
		$stmt->execute(['nai_id' => $nai_id]);
		$data = $stmt->fetch();

		if ($data) {
			return new Agent($data);
		}

		return false;
	}

	/**
	 * Creates an Agent
	 * @param  Agent  $agent 
	 * @return Agent
	 */
	public function insert(Agent $agent)
	{
		$data = $agent->getArrayCopy();
		
		$query = 'INSERT INTO agent 
		(nai_id, name, firstName, lastName, defaultPhotoURL, emailAddress, businessPhone, mobilePhone, specialty) 
		VALUES (:nai_id, :name, :firstName, :lastName, :defaultPhotoURL, :emailAddress, :businessPhone, :mobilePhone, :specialty)';
		$stmt = $this->db->prepare($query);
		//Remove 'id' as it will be autogenerated
		unset($data['id']);
		$stmt->execute($data);
		return new Agent($data);
		
		
	}

	/**
	 * Creates an Agent If It doesn't Exist
	 * @param  Agent  $agent 
	 * @return Agent
	 */
	public function insertIfNotExist(Agent $agent)
	{
		$data = $agent->getArrayCopy();
		
		//If Agent doesn't Exsist import it
		if(!$this->getByNaiId($data['nai_id'])){
			return $this->insert($agent);
		}
		return false;
		
	}

	public function update(Agent $agent)
	{
		$data = $agent->getArrayCopy();
		$query = 'UPDATE agent 
		SET name = :name,
		firstName = :firstName,
		lastName = :lastName,
		defaultPhotoURL = :defaultPhotoURL,
		emailAddress = :emailAddress, 
		businessPhone = :businessPhone,
		mobilePhone = :mobilePhone,
		specialty = :specialty
		WHERE nai_id = :nai_id
		';

		$stmt = $this->db->prepare($query);
		$result = $stmt->execute($data);

		return new Agent($data);
	}

	/**
	 * Deletes an Agent
	 * @param  int $id 
	 * @return boolean True if there was an Agent to be Deleted
	 */
	public function delete($nai_id)
	{
		$query = 'DELETE FROM agent WHERE nai_id = :nai_id';
		$stmt = $this->db->prepare($query);
		$stmt->execute(['nai_id' => $nai_id]);
		return (bool)$stmt->rowCount();
	}

	/**
	 * Drop Table
	 * @return boolean 
	 */
	public function drop()
	{
		$query = 'DELETE FROM agent';
		$count = $this->db->exec($query);
		return ($count > 0) ?? false;
	}

}