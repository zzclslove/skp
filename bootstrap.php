<?php
/*
 * Sample bootstrap file.
 */

require __DIR__ . '\vendor\autoload.php';
require __DIR__ . '\vendor\common.php';

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

// Suppress DateTime warnings, if not set already
date_default_timezone_set(@date_default_timezone_get());

// Adding Error Reporting for understanding errors properly
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Replace these values by entering your own ClientId and Secret by visiting https://developer.paypal.com/webapps/developer/applications/myapps
$clientId = 'AZg1Apg5qDkD7wFVwYlGuZmZvbqDnRD6MVJf_d1igTE6i8UNzoY39R1wWEMtr8qkXyoJvnpx5suzUyj3';
$clientSecret = 'EP6JougtuSuU2ESdG9vjNKOxcligtC8HvRaT5Zfo4eGejAzzcTeRkXsTrmmqW4HmoqoTOac0TYqeoUK0';

//$clientId = 'AV_uLvs2lpwsLgwzjkVg9T8VuYX7Qc0HDJwVJuA0MH-otq-t2A2czfCWaCg3PPZX9WChNHJweZc1h7l4';
//$clientSecret = 'EJxGJ55nRP3M_KvcfpK-kPSwRMDn8G0ged7tszmmMnnrsszN-wrzfhK-pKb6rZKChgfz874LyDoKsaM0';


/**
 * All default curl options are stored in the array inside the PayPalHttpConfig class. To make changes to those settings
 * for your specific environments, feel free to add them using the code shown below
 * Uncomment below line to override any default curl options.
 */
//PayPalHttpConfig::$defaultCurlOptions[CURLOPT_SSLVERSION] = CURL_SSLVERSION_TLSv1_2;


/** @var \Paypal\Rest\ApiContext $apiContext */
$apiContext = getApiContext($clientId, $clientSecret);

return $apiContext;
/**
 * Helper method for getting an APIContext for all calls
 * @param string $clientId Client ID
 * @param string $clientSecret Client Secret
 * @return PayPal\Rest\ApiContext
 */
function getApiContext($clientId, $clientSecret)
{

    // #### SDK configuration
    // Register the sdk_config.ini file in current directory
    // as the configuration source.
    /*
    if(!defined("PP_CONFIG_PATH")) {
        define("PP_CONFIG_PATH", __DIR__);
    }
    */


    // ### Api context
    // Use an ApiContext object to authenticate
    // API calls. The clientId and clientSecret for the
    // OAuthTokenCredential class can be retrieved from
    // developer.paypal.com

    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            $clientId,
            $clientSecret
        )
    );

    // Comment this line out and uncomment the PP_CONFIG_PATH
    // 'define' block if you want to use static file
    // based configuration

    $apiContext->setConfig(
        array(
            'mode' => 'live',
            'log.LogEnabled' => false,
            'cache.enabled' => true
        )
    );

    // Partner Attribution Id
    // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
    // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
    // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

    return $apiContext;
}
