# eiu-ll-integration
This is a PHP client library for the LibLynx Connect identity, registration, subscriptions and access management API.
The API allows the publisher to control access to electronic resources without worrying about the method used.

* The library interacts with the LibLynx Integration API via HTTP requests.
* Guzzle is used for sending requests.
* OAuth2 is used for authentication.
* The results of requests are processed and represented as objects (resources).
* Caching of request results is implemented.

# Library Structure

### 1. Class Client
This class represents a client for interacting with the LibLynx Integration API. It provides methods for sending requests to the API.

### 2. Class HTTPClientFactory
This class provides means for creating an HTTP client capable of OAuth2 and caching.

### 3. Request Classes (AccountRequest, IdentificationRequest, RegistrationRequest, SubscriptionRequest)
These classes are concrete implementations of the abstract class AbstractApiRequest for different types of requests.

### 4. Resource Classes (Account, Identification, Registration, Subscription)
These classes are concrete implementations of the abstract class AbstractApiResource for different types of resources.

## Setting API credentials
In order to use this, you will need an API client id and client secret from LibLynx.
You can set the following environment variables to avoid placing credentials in your code:
* LIBLYNX_CLIENT_ID
* LIBLYNX_CLIENT_SECRET

## Examples
Create an instance of the Client class, passing the client ID and secret in the constructor.
Create request for the desired operation.
Use the sendRequest method to send requests to the API.
Handle the results of the requests using the corresponding resources.

```
$llClient = new Client(LIBLYNX_CLIENT_ID, LIBLYNX_CLIENT_ID);

$logger = new DiagnosticLogger;
$llClient->setLogger($logger);

$cache = new ArrayCache();
$llClient->setCache($cache);

$request = IdentificationRequest::getRequestDataJSON();

$identification = $llClient->sendRequest($request);
if ($identification->getStatus() == 'identified') {
    //visitor is identified, you can now check their access rights
} elseif ($identification->getStatus() == 'wayf') {
    //to find out who the visitor is, redirect to WAYF page
    $url = $identification->getWayfUrl();
    header("Location: $url");
    exit;
} else {
    //liblynx failed - check diagnostic logs
}
```
