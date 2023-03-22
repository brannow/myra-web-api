<?php
declare(strict_types=1);

namespace Myracloud\WebApi\Endpoint;

use GuzzleHttp\RequestOptions;

/**
 * Class DnsRecord
 *
 * @package Myracloud\WebApi\Endpoint
 */
class DnsRecord extends AbstractEndpoint
{
    /**
     * @var string
     */
    protected $epName = 'dnsRecords';

    /**
     * @param      $domain
     * @param int  $page
     * @param null $search
     * @param null $recordType
     * @param bool $activeOnly
     * @param bool $loadbalancedOnly
     * @return mixed
     * @throws \Exception
     */
    public function getList(
        $domain,
        int $page = 1,
        $search = null,
        $recordType = null,
        $activeOnly = false,
        $loadbalancedOnly = false
    ) {
        $uri = $this->uri . '/' . $domain . '/' . $page;

        $queryParams = [];
        if (!empty($search)) {
            $queryParams['search'] = $search;
        }
        if (!empty($recordType)) {
            $this->validateDnsType($recordType);
            $queryParams['recordTypes'] = $recordType;
        }
        if ($activeOnly == true) {
            $queryParams['activeOnly'] = true;
        }
        if ($loadbalancedOnly == true) {
            $queryParams['loadbalancer'] = true;
        }

        $uri .= '?' . http_build_query($queryParams);
        /** @var \GuzzleHttp\Psr7\Response $res */
        $res = $this->client->get($uri);

        return $this->handleResponse($res);
    }

    /**
     * @param        $domain
     * @param        $subdomain
     * @param        $ipAddress
     * @param        $ttl
     * @param string $recordType
     * @param bool   $active
     * @param null   $sslCertTemplate
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(
        $domain,
        $subdomain,
        $ipAddress,
        $ttl,
        $recordType = 'A',
        $active = true,
        $sslCertTemplate = null
    ) {
        $uri = $this->uri . '/' . $domain;


        $this->validateDnsType($recordType);

        $options[RequestOptions::JSON] =
            [
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType,
                'active'     => $active,
            ];

        if ($sslCertTemplate != null) {
            $options[RequestOptions::JSON]['sslCertTemplate'] = $sslCertTemplate;
        }
        /** @var \GuzzleHttp\Psr7\Response $res */
        $res = $this->client->request('PUT', $uri, $options);

        return $this->handleResponse($res);
    }

    /**
     * @param           $domain
     * @param           $id
     * @param \DateTime $modified
     * @param           $subdomain
     * @param           $ipAddress
     * @param           $ttl
     * @param string    $recordType
     * @param bool      $active
     * @param null      $sslCertTemplate
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(
        $domain,
        $id,
        \DateTime $modified,
        $subdomain,
        $ipAddress,
        $ttl,
        $recordType = 'A',
        $active = true,
        $sslCertTemplate = null
    ) {

        $uri = $this->uri . '/' . $domain;

        $this->validateDnsType($recordType);

        $options[RequestOptions::JSON] =
            [
                "id"         => $id,
                'modified'   => $modified->format('c'),
                'name'       => $subdomain,
                'value'      => $ipAddress,
                'ttl'        => $ttl,
                'recordType' => $recordType,
                'active'     => $active,
            ];
        if ($sslCertTemplate != null) {
            $options[RequestOptions::JSON]['sslCertTemplate'] = $sslCertTemplate;
        }
        /** @var \GuzzleHttp\Psr7\Response $res */
        $res = $this->client->request('POST', $uri, $options);

        return $this->handleResponse($res);
    }
}