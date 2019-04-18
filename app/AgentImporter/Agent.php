<?php

namespace App\AgentImporter;

class Agent
{
	private $id;
	private $nai_id;
	private $name;
	private $firstName;
	private $lastName;
	private $defaultPhotoURL;
	private $emailAddress;
	private $businessPhone;
	private $mobilePhone;
	private $specialty;

	public function __construct(array $data)
	{
		$this->nai_id = $data['nai_id'] ?? null;
		$this->insertOrUpdate($data);
	}

	public function getArrayCopy()
	{
		return [
			'id' => $this->id,
			'nai_id' => $this->nai_id,
			'name' => $this->name,
			'firstName' => $this->firstName,
			'lastName' => $this->lastName,
			'defaultPhotoURL' => $this->defaultPhotoURL,
			'emailAddress' => $this->emailAddress,
			'businessPhone' => $this->businessPhone,
			'mobilePhone' => $this->mobilePhone,
			'specialty' => $this->specialty
		];
	}

	public function update(array $data)
	{
		$this->insertOrUpdate($data);
	}

	public function insertOrUpdate(array $data)
	{
		$this->nai_id = $data['nai_id'] ?? null;
		$this->name = $data['name'] ?? null;
		$this->firstName = $data['firstName'] ?? null;
		$this->lastName = $data['lastName'] ?? null;
		$this->defaultPhotoURL = $data['defaultPhotoURL'] ?? null;
		$this->emailAddress = $data['emailAddress'] ?? null;
		$this->businessPhone = $data['businessPhone'] ?? null;
		$this->mobilePhone = $data['mobilePhone'] ?? null;
		$this->specialty = $data['specialty'] ?? null;
	}

	public function getNaiId()
	{
		return $this->nai_id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getFullName()
	{
		return $this->name;
	}
}