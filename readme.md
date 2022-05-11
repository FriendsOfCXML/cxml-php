This repository is a public fork of https://github.com/mathielen/cxml implementation.

# What is it?

> cXML is a streamlined protocol intended for consistent communication of business documents between procurement
> applications, e-commerce hubs and suppliers. http://cxml.org/
>
> cXML Reference Guide (PDF): http://xml.cxml.org/current/cXMLReferenceGuide.pdf

# Status

|  CXML Version | Status Test  |
|---|---|
| 1.2.050  | OK |
| 1.2.053  | OK |

# Getting Started

## Installation

```bash
$ composer require loeffelhardt/el-cxml
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
$credentialRegistry = new \Mathielen\CXml\Credential\CredentialRegistry();

$someSupplier = new \Mathielen\CXml\Model\Credential('DUNS', 12345);
$credentialRegistry->registerCredential($someSupplier);

$someBuyer = new \Mathielen\CXml\Model\Credential('my-id-type', "buyer@domain.com");
$credentialRegistry->registerCredential($someBuyer);

$someHub = new \Mathielen\CXml\Model\Credential('my-id-type', "hub@domain.com", "abracadabra");
$credentialRegistry->registerCredential($someHub);
```

### Register Handler

```php
$handlerRegistry = new \Mathielen\CXml\Handler\HandlerRegistry();

$handlerRegistry->register(new Mathielen\CXml\Handler\Request\SelfAwareProfileRequestHandler(...));
$handlerRegistry->register(new Mathielen\CXml\Handler\Request\StaticStartPagePunchOutSetupRequestHandler(...));
$handlerRegistry->register(new MyOrderRequestHandler());
$handlerRegistry->register(new MyStatusUpdateRequestHandler());
...
```

### Build cXML

```php
//$payload = new \Mathielen\CXml\Model\Message\...Message(...);
//or...
//$payload = new \Mathielen\CXml\Model\Request\...Request(...);
//or...
$payload = new \Mathielen\CXml\Model\Response\...Response(...);

$cXml = \Mathielen\CXml\Builder::create()
    ->payload($payload)
    ->build();

$payload = new \Mathielen\CXml\Model\Request\...Request(...);
$cXml = \Mathielen\CXml\Builder::create()
    ->payload($payload)
    ->from(...)
    ->to(...)
    ->sender(...)
    ->build();
```

### Register outgoing cXML documents

You may want to register sent-out documents so they can be referenced by subsequent request-documents via payloadId.

```php
$documentRegistory = new MyDocumentRegistry(); //implements Mathielen\CXml\Document\DocumentRegistryInterface

$documentRegistory->register($cXml);
```

### Process incoming cXML documents

```php
$headerProcessor = new \Mathielen\CXml\Processor\HeaderProcessor($credentialRegistry);

$cXmlProcessor = new \Mathielen\CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$cXmlProcessor->process($cXml);
```

### Putting it all together

```php
$credentialRegistry = new \Mathielen\CXml\Credential\CredentialRegistry();
//TODO register...

$handlerRegistry = new \Mathielen\CXml\Handler\HandlerRegistry();
//TODO register...

$builder = \Mathielen\CXml\Builder::create();

$headerProcessor = new \Mathielen\CXml\Processor\HeaderProcessor($credentialRegistry);
$cXmlProcessor = new \Mathielen\CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$pathToDtd = '.'; //point the directory with extracted contents of zip-file with the DTDs, downloaded from cxml.org
$dtdValidator = new \Mathielen\CXml\Validation\DtdValidator($pathToDtd);

$endpoint = new \Mathielen\CXml\Endpoint(
    $dtdValidator,
    $cXmlProcessor
);

//$xmlString could be the body of an incoming http request
$xmlString = '<cXML>...</cXML>';
$result = $endpoint->parseAndProcessStringAsCXml($xmlString);

//$result could be null (i.e. for a Response or Message) or another CXml object which would be the Response to a Request
//you would have to handle the transport yourself
```
