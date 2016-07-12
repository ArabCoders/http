<?php
/**
 * This file is part of {@see \arabcoders\http} package.
 *
 * (c) 2013-2016 Abdul.Mohsen B. A. A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace arabcoders\http\Interfaces;

/**
 * Describes a curl aware instance.
 *
 * @package    {@see \arabcoders\http}.
 * @author     Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */

interface HttpInterface
{
    /**
     * Curl GET
     *
     * @param  string $url            Url.
     * @param  int    $connectTimeout Connection timeout.
     * @param  int    $executeTimeout Execution timeout.
     *
     * @return string   response
     */
    public function get( string $url, int $connectTimeout, int $executeTimeout ): string;

    /**
     * CURL POST
     *
     * @param string $url            Url.
     * @param array  $vars           request parameters
     * @param int    $connectTimeout Connection timeout
     * @param int    $executeTimeout Execution timeout
     *
     * @return string response
     */
    public function post( string $url, array $vars = [ ], int $connectTimeout, int $executeTimeout ): string;
    
    /**
     * Set Request Headers
     *
     * @param array $headers headers as key/value pair.
     *
     * @return HttpInterface
     */
    public function setRequestHeaders( array $headers = [ ] ):HttpInterface;

    /**
     * Delete Request Headers
     *
     * @param array $headers headers as key/value pair.
     *
     * @return HttpInterface
     */
    public function deleteRequestHeaders( array $headers = [ ] ): HttpInterface;

    /**
     * Get Request Headers
     *
     * @return array
     */
    public function getRequestHeaders(): array;

    /**
     * Set Request Headers
     *
     * @param string $url     URL
     * @param string $file    file location.
     * @param string $name    field name (eg $_FILES[NAME]).
     * @param array  $params  other request parameters.
     * @param array  $options options.
     *
     * @return string
     */
    public function upload( string $url, string $file, string $name, array $params = [ ], array $options = [ ] ): string;

    /**
     * set cert file.
     *
     * @param string $cert
     *
     * @return HttpInterface
     */
    public function setCert( string $cert ): HttpInterface;

    /**
     * set debug state.
     *
     * @param bool $state
     *
     * @return HttpInterface
     */
    public function setDebug( bool $state ): HttpInterface;

    /**
     * get debug information.
     *
     * @return array
     */
    public function getDebug(): array;

    /**
     * Set Authorization.
     *
     * @param string $user
     * @param string $password
     *
     * @return HttpInterface
     */
    public function setAuth( string $user, string $password ): HttpInterface;

    /**
     * Set CURTOPT_* options.
     *
     * @param array $array
     *
     * @return HttpInterface
     */
    public function setOpts( array $array ): HttpInterface;
}