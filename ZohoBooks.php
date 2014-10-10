<?php

/**
 *
 * Zoho Books API
 * Version: 2
 *
 * Author: Giuseppe Occhipinti - https://github.com/peppeocchi
 *
 * CHANGELOG v2
 * - extended parameters for invoices and credit notes and contacts
 *
 */

class ZohoBooks
{
	/**
	 * cUrl timeout
	 */
	private $timeout = 30;

	/**
	 * HTTP code of the cUrl request
	 */
	private $httpCode;

	/**
	 * Zoho Books API authentication
	 */
	private $authtoken;
	private $organizationId;

	/**
	 * Zoho Books API request limit management
	 */
	private $apiRequestsLimit = 150;
	private $apiRequestsCount;
	private $apiTimeLimit = 60;
	private $startTime;

	/**
	 * Zoho Books API urls request
	 */
	private $apiUrl = 'https://books.zoho.com/api/v3/';
	private $contactsUrl = 'contacts/';
	private $invoicesUrl = 'invoices/';
	private $creditnotesUrl = 'creditnotes/';

	

	/**
	 * Init
	 *
	 * @param (string) Zoho Books authentication token
	 * @param (string) Zoho Books organization id
	 */
	public function __construct($authtoken, $organizationId)
	{
		$this->authtoken = $authtoken;
		$this->organizationId = $organizationId;
		$this->apiRequestsCount = 0;
		$this->startTime = time();
	}


	/**
	 * Get all contacts
	 *
	 * @return (string) json string || false
	 */
	public function allContacts($config = array())
	{
		$url = $this->apiUrl . $this->contactsUrl . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId;
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$contacts = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $contacts : false;
	}

	/**
	 * Get contact details by ID
	 *
	 * @param (int) contact id
	 *
	 * @return (string) json string || false
	 */
	public function getContact($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->contactsUrl . $id . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$contact = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $contact : false;
	}


	/**
	 * Get all invoices
	 *
	 * @param (date) date start
	 * @param (date) date end
	 *
	 * @return (string) json string || false
	 */
	public function allInvoices($config = array())
	{
		$url = $this->apiUrl . $this->invoicesUrl . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId;
		if(isset($config['date_start']) && isset($config['date_end'])) {
			$url .= '&date_start=' . $config['date_start'] . '&date_end=' . $config['date_end'];
		}
		if(isset($config['invoice_number_startswith'])) {
			$url .= '&invoice_number_startswith=' . $config['invoice_number_startswith'];
		}
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$invoices = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $invoices : false;
	}


	/**
	 * Get invoice
	 *
	 * @param (int) invoice id
	 *
	 * @return (string) json string || false
	 */
	public function getInvoice($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->invoicesUrl . $id . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$invoice = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $invoice : false;
	}


	/**
	 * Create an invoice
	 *
	 * @param (string) json encoded
	 * @param (bool) send the invoice to the contact associated with the invoice
	 *
	 * @return (bool)
	 */
	public function postInvoice($invoice, $send = false)
	{
		$url = $this->apiUrl . $this->invoicesUrl;

		$data = array(
			'authtoken' 		=> $this->authtoken,
			'JSONString' 		=> $invoice,
			"organization_id" 	=> $this->organizationId
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER => true
		));

		$invoice = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 201 ? true : false;
	}


	/**
	 * Get all credit notes
	 *
	 * @param (date) date start
	 * @param (date) date end
	 *
	 * @return (string) json string || false
	 */
	public function allCreditNotes($config = array())
	{
		$url = $this->apiUrl . $this->creditnotesUrl . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId;
		if(isset($config['date_start']) && isset($config['date_end'])) {
			$url .= '&date_start=' . $config['date_start'] . '&date_end=' . $config['date_end'];
		}
		if(isset($config['page'])) {
			$url .= '&page=' . $config['page'];
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$creditnotes = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $creditnotes : false;
	}


	/**
	 * Get credit note
	 *
	 * @param (int) credit note id
	 *
	 * @return (string) json string || false
	 */
	public function getCreditNote($id)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->creditnotesUrl . $id . '?authtoken=' . $this->authtoken . '&organization_id=' . $this->organizationId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$creditnote = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 200 ? $creditnote : false;
	}


	/**
	 * Create a credit note
	 *
	 * @param (string) json string
	 *
	 * @return (bool)
	 */
	public function postCreditNote($creditnote)
	{
		$url = $this->apiUrl . $this->creditnotesUrl;

		$data = array(
			'authtoken' 		=> $this->authtoken,
			'JSONString' 		=> $creditnote,
			"organization_id" 	=> $this->organizationId
		);

		$ch = curl_init($url);

		curl_setopt_array($ch, array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER => true
		));

		$creditnote = curl_exec($ch);
		$this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$this->checkApiRequestsLimit();

		return $this->httpCode == 201 ? true : false;
	}


	/**
	 * Get HTTP code
	 */
	public function getHttpCode()
	{
		return $this->httpCode ? $this->httpCode : false;
	}


	/**
	 * Check API requests limit
	 *
	 */
	private function checkApiRequestsLimit()
	{
		$tempTime = time() - $this->startTime;
		if($this->apiRequestsCount >= $this->apiRequestsLimit && $tempTime < $this->apiTimeLimit) {
			usleep(($this->apiTimeLimit - $tempTime)*1000000);
			$this->apiRequestsCount = 1;
			$this->startTime = time();
		} else {
			$this->apiRequestsCount++;
		}
	}
}