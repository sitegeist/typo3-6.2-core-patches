<?php
namespace TYPO3\CMS\Extbase\Mvc\Web;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Represents a web request.
 *
 * @api
 */
class Request extends \TYPO3\CMS\Extbase\Mvc\Request {

	/**
	 * @var \TYPO3\CMS\Extbase\Security\Cryptography\HashService
	 * @inject
	 */
	protected $hashService;

	/**
	 * @var string The requested representation format
	 */
	protected $format = 'html';

	/**
	 * @var string Contains the request method
	 */
	protected $method = 'GET';

	/**
	 * @var string
	 */
	protected $requestUri;

	/**
	 * @var string The base URI for this request - ie. the host and path leading to the index.php
	 */
	protected $baseUri;

	/**
	 * @var boolean TRUE if the HMAC of this request could be verified, FALSE otherwise
	 * @deprecated since Extbase 1.4.0, will be removed two versions after Extbase 6.1
	 */
	protected $hmacVerified = FALSE;

	/**
	 * @var boolean TRUE if the current request is cached, false otherwise.
	 */
	protected $isCached = FALSE;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * @var \TYPO3\CMS\Extbase\Service\EnvironmentService
	 * @inject
	 */
	protected $environmentService;

	/**
	 * Sets the request method
	 *
	 * @param string $method Name of the request method
	 * @return void
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidRequestMethodException if the request method is not supported
	 */
	public function setMethod($method) {
		if ($method === '' || strtoupper($method) !== $method) {
			throw new \TYPO3\CMS\Extbase\Mvc\Exception\InvalidRequestMethodException('The request method "' . $method . '" is not supported.', 1217778382);
		}
		$this->method = $method;
	}

	/**
	 * Returns the name of the request method
	 *
	 * @return string Name of the request method
	 * @api
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Sets the request URI
	 *
	 * @param string $requestUri URI of this web request
	 * @return void
	 */
	public function setRequestUri($requestUri) {
		$this->requestUri = $requestUri;
	}

	/**
	 * Returns the request URI
	 *
	 * @return string URI of this web request
	 * @api
	 */
	public function getRequestUri() {
		return $this->requestUri;
	}

	/**
	 * Sets the base URI for this request.
	 *
	 * @param string $baseUri New base URI
	 * @return void
	 */
	public function setBaseUri($baseUri) {
		$this->baseUri = $baseUri;
	}

	/**
	 * Returns the base URI
	 *
	 * @return string Base URI of this web request
	 * @api
	 */
	public function getBaseUri() {
		if ($this->environmentService->isEnvironmentInBackendMode()) {
			return $this->baseUri . TYPO3_mainDir;
		} else {
			return $this->baseUri;
		}
	}

	/**
	 * Could the request be verified via a HMAC?
	 *
	 * @param boolean $hmacVerified TRUE if request could be verified, FALSE otherwise
	 * @return void
	 * @deprecated since Extbase 1.4.0, will be removed two versions after Extbase 6.1
	 */
	public function setHmacVerified($hmacVerified) {
		$this->hmacVerified = (boolean) $hmacVerified;
	}

	/**
	 * Could the request be verified via a HMAC?
	 *
	 * @return boolean TRUE if request could be verified, FALSE otherwise
	 * @deprecated since Extbase 1.4.0, will be removed two versions after Extbase 6.1
	 */
	public function isHmacVerified() {
		return $this->hmacVerified;
	}

	/**
	 * Set if the current request is cached.
	 *
	 * @param boolean $isCached
	 */
	public function setIsCached($isCached) {
		$this->isCached = (boolean) $isCached;
	}

	/**
	 * Return whether the current request is a cached request or not.
	 *
	 * @api (v4 only)
	 * @return boolean the caching status.
	 */
	public function isCached() {
		return $this->isCached;
	}

	/**
	 * Get a freshly built request object pointing to the Referrer.
	 *
	 * @return Request the referring request, or NULL if no referrer found
	 */
	public function getReferringRequest() {
		$referrerArray = $this->getValidatedReferrerArguments();
		if ($referrerArray !== NULL) {
			$arguments = array();
			if (isset($this->internalArguments['__referrer']['arguments'])) {
				// This case is kept for compatibility in 7.6 and 6.2, but will be removed in 8
				$arguments = unserialize(base64_decode($this->hashService->validateAndStripHmac($this->internalArguments['__referrer']['arguments'])));
			}
			$referringRequest = new ReferringRequest();
			$referringRequest->setArguments(\TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule($arguments, $referrerArray));
			return $referringRequest;
		}
		return NULL;
	}

	/**
	 * @return array|NULL
	 * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException
	 * @throws \TYPO3\CMS\Extbase\Security\Exception\InvalidHashException
	 */
	public function getValidatedReferrerArguments() {
		if (isset($this->internalArguments['__referrer']['@request'])) {
			$referrerArguments = unserialize($this->hashService->validateAndStripHmac($this->internalArguments['__referrer']['@request']));
			if (!is_array($referrerArguments)) {
				$referrerArguments = NULL;
			}
			return $referrerArguments;
		}
		return NULL;
	}
}
