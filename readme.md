# What is it?

> cXML is a streamlined protocol intended for consistent communication of business documents between procurement
> applications, e-commerce hubs and suppliers. http://cxml.org/
>
> cXML Reference Guide (PDF): http://xml.cxml.org/current/cXMLReferenceGuide.pdf

# Status

| CXML Version | Status Test |
|--------------|-------------|
| 1.2.050      | OK          |
| 1.2.053      | OK          |

# Getting Started

## Installation

```bash
$ composer require friendsofcxml/cxml-php
```

Then include Composer’s autoloader:

```php
require_once 'vendor/autoload.php';
```

## Get current dtd definition files

1. Download get current Specification from http://cxml.org/downloads.html
2. Extract files
3. Use cXML.dtd for validation (see below)

## Quickstart

### Identity and credentials

```php
//we use a basic registry here. You could use your own (db-based?) repository that implements CredentialRepositoryInterface
$credentialRegistry = new \CXml\Credential\CredentialRegistry();

$someSupplier = new \CXml\Model\Credential('DUNS', 12345);
$credentialRegistry->registerCredential($someSupplier);

$someBuyer = new \CXml\Model\Credential('my-id-type', "buyer@domain.com");
$credentialRegistry->registerCredential($someBuyer);

$someHub = new \CXml\Model\Credential('my-id-type', "hub@domain.com", "abracadabra");
$credentialRegistry->registerCredential($someHub);
```

### Register Handler

```php
$handlerRegistry = new \CXml\Handler\HandlerRegistry();

$handlerRegistry->register(new CXml\Handler\Request\SelfAwareProfileRequestHandler(...));
$handlerRegistry->register(new CXml\Handler\Request\StaticStartPagePunchOutSetupRequestHandler(...));
$handlerRegistry->register(new MyOrderRequestHandler());
$handlerRegistry->register(new MyStatusUpdateRequestHandler());
...
```

### Build cXML

```php
//$payload = new \CXml\Model\Message\...Message(...);
//or...
//$payload = new \CXml\Model\Request\...Request(...);
//or...
$payload = new \CXml\Model\Response\...Response(...);

$cXml = \CXml\Builder::create()
    ->payload($payload)
    ->build();

$payload = new \CXml\Model\Request\...Request(...);
$cXml = \CXml\Builder::create()
    ->payload($payload)
    ->from(...)
    ->to(...)
    ->sender(...)
    ->build();
```

### Register outgoing cXML documents

You may want to register sent-out documents so they can be referenced by subsequent request-documents via payloadId.

```php
$documentRegistory = new MyDocumentRegistry(); //implements CXml\Document\DocumentRegistryInterface

$documentRegistory->register($cXml);
```

### Process incoming cXML documents

```php
$headerProcessor = new \CXml\Processor\HeaderProcessor($credentialRegistry);

$cXmlProcessor = new \CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$cXmlProcessor->process($cXml);
```

### Putting it all together

```php
$credentialRegistry = new \CXml\Credential\CredentialRegistry();
//TODO register...

$handlerRegistry = new \CXml\Handler\HandlerRegistry();
//TODO register...

$builder = \CXml\Builder::create();

$headerProcessor = new \CXml\Processor\HeaderProcessor($credentialRegistry);
$cXmlProcessor = new \CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$pathToDtd = '.'; //point the directory with extracted contents of zip-file with the DTDs, downloaded from cxml.org
$dtdValidator = new \CXml\Validation\DtdValidator($pathToDtd);

$endpoint = new \CXml\Endpoint(
    $dtdValidator,
    $cXmlProcessor
);

//$xmlString could be the body of an incoming http request
$xmlString = '<cXML>...</cXML>';
$result = $endpoint->parseAndProcessStringAsCXml($xmlString);

//$result could be null (i.e. for a Response or Message) or another CXml object which would be the Response to a Request
//you would have to handle the transport yourself
```
# Credits
- Markus Thielen (https://github.com/mathielen)
