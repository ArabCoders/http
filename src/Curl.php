<?php
/**
 * This file is part of {@see \arabcoders\http} package.
 *
 * (c) 2013-2016 Abdul.Mohsen B. A. A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace arabcoders\http;
use arabcoders\http\Interfaces\HttpInterface;

/**
 * Curl
 *
 * @package \arabcoders\http
 * @author  Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */
class Curl implements HttpInterface
{
    /**
     * @var int Default timeout.
     */
    const DEFUALT_TIMEOUT = 250;

    /**
     * @var int Default connection timeout
     */
    const DEFAULT_CONNECTTIMEOUT = 120;

    /**
     * @var string The user agent to send to the URL.
     */
    protected $agent;

    /**
     * @var array The Request Headers.
     */
    protected $headers = [ ];

    /**
     * @var array Set curl opts.
     */
    protected $opts = [ ];

    /**
     * @var string ca cert.
     */
    protected $cert;

    /**
     * @var bool enable debugging.
     */
    protected $debugState = false;

    /**
     * @var array debugging data.
     */
    protected $debug = [ ];

    /**
     * not sure why?
     *
     * @var string
     */
    private $output;

    public function __construct( array $options = [ ] )
    {
        if ( !extension_loaded( 'curl' ) )
        {
            throw new \RuntimeException( 'curl extension is not loaded.' );
        }

        if ( isset( $options['agent'] ) )
        {
            $this->agent = $options['agent'];
        }

        if ( isset( $options['cert'] ) )
        {
            $this->setCert( $options['cert'] );
        }

        if ( !empty( $options['headers'] ) )
        {
            $this->setRequestHeaders( $options['headers'] );
        }
    }

    public function get( string $url, int $connectTimeout = self::DEFAULT_CONNECTTIMEOUT, int $executeTimeout = self::DEFUALT_TIMEOUT ): string
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $executeTimeout );
        curl_setopt( $ch, CURLOPT_USERAGENT, $this->agent );

        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_CAINFO, $this->cert );

        if ( sizeof( $this->opts ) >= 1 )
        {
            foreach ( $this->opts as $key => $value )
            {
                curl_setopt( $ch, $key, $value );
            }
        }

        if ( sizeof( $this->headers ) >= 1 )
        {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders() );
        }

        $this->output = curl_exec( $ch );

        if ( $this->debugState )
        {
            $this->debug = [
                'errno' => curl_errno( $ch ),
                'error' => curl_error( $ch ),
                'info'  => curl_getinfo( $ch )
            ];
        }

        curl_close( $ch );

        return $this->output;
    }

    public function post( string $url, array $vars = [ ], int $connectTimeout = self::DEFAULT_CONNECTTIMEOUT, int $executeTimeout = self::DEFUALT_TIMEOUT ): string
    {
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $vars );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $executeTimeout );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, $this->agent );

        //-- ssl.
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_CAINFO, $this->cert );

        if ( sizeof( $this->opts ) >= 1 )
        {
            foreach ( $this->opts as $key => $value )
            {
                curl_setopt( $ch, $key, $value );
            }
        }

        if ( sizeof( $this->headers ) >= 1 )
        {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->getRequestHeaders() );
        }

        $output = curl_exec( $ch );
        $error  = curl_error( $ch );
        $info   = curl_getinfo( $ch );

        if ( $this->debugState )
        {
            $this->debug = [
                'errno' => curl_errno( $ch ),
                'error' => $error,
                'info'  => $info,
            ];
        }

        curl_close( $ch );

        return $output;
    }

    public function setRequestHeaders( array $headers = [ ] ): HttpInterface
    {
        if ( count( $headers ) >= 1 )
        {
            foreach ( $headers as $key => $value )
            {
                $this->headers[$key] = $value;
            }
        }

        return $this;
    }

    public function deleteRequestHeaders( array $headers = [ ] ): HttpInterface
    {
        if ( count( $headers ) >= 1 )
        {
            foreach ( $headers as $key => $value )
            {
                if ( array_key_exists( $key, $this->headers ) )
                {
                    unset( $this->headers[$key] );
                }
            }
        }

        return $this;
    }

    public function getRequestHeaders(): array
    {

        if ( count( $this->headers ) >= 1 )
        {
            $requestHeaders = [ ];

            foreach ( $this->headers as $key => $value )
            {
                $requestHeaders[] = "{$key}:{$value}";
            }

            return $requestHeaders;
        }

        return [ ];
    }

    Public function upload( string $url, string $file, string $name, array $params = [ ], array $options = [ ] ): string
    {
        $postParams = [ ];

        if ( !empty( $params ) AND sizeof( $params ) >= 1 )
        {
            foreach ( $params as $key => $value )
            {
                $postParams[$key] = $value;
            }
        }

        $postParams[$name] = new \CurlFile( $file );

        return $this->post( $url, $postParams );
    }

    public function setCert( string $cert ): HttpInterface
    {
        if ( !is_readable( $cert ) )
        {
            throw new \RuntimeException( sprintf( '%s is not readable.', $cert ) );
        }

        $this->cert = $cert;

        return $this;
    }

    public function setDebug( bool $state ): Interfaces\HttpInterface
    {
        $this->debugState = $state;

        return $this;
    }

    public function getDebug(): array
    {
        if ( !$this->debugState )
        {
            throw new \RuntimeException( 'Debugging is not enabled.' );
        }

        if ( empty( $this->debug ) )
        {
            throw new \RuntimeException( 'There is no debugging data, most likely you enabled debugging after the request has finished.' );
        }

        return $this->debug;
    }

    public function setAuth( string $user, string $password ): HttpInterface
    {
        return $this->setOpts( [ CURLOPT_USERPWD => $user . ':' . $password ] );
    }

    public function setOpts( array $array ): HttpInterface
    {
        if ( count( $array ) >= 1 )
        {
            foreach ( $array as $key => $value )
            {
                $this->opts[$key] = $value;
            }
        }

        return $this;
    }
}
