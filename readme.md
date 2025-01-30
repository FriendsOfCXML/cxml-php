# What is it?

> cXML is a streamlined protocol intended for consistent communication of business documents between procurement
> applications, e-commerce hubs and suppliers. http://cxml.org/
>
> cXML Reference Guide (PDF): http://xml.cxml.org/current/cXMLReferenceGuide.pdf

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
$credentialRegistry = new \CXml\Credential\Registry();

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
$headerProcessor = new \CXml\Processor\HeaderProcessor($credentialRegistry, $credentialRegistry);

$cXmlProcessor = new \CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$cXmlProcessor->process($cXml);
```

### Putting it all together

```php
$credentialRegistry = new \CXml\Credential\Registry();
//TODO register credentials...

$handlerRegistry = new \CXml\Handler\HandlerRegistry();
//TODO register handler...

$builder = \CXml\Builder::create();

$headerProcessor = new \CXml\Processor\HeaderProcessor($credentialRegistry, $credentialRegistry);
$cXmlProcessor = new \CXml\Processor\Processor(
  $headerProcessor, 
  $handlerRegistry,
  $builder
);

$pathToDtd = '.'; //point the directory with extracted contents of zip-file with the DTDs, downloaded from cxml.org
$dtdValidator = \CXml\Validation\DtdValidator::fromDtdDirectory($pathToDtd);
$serializer = \CXml\Serializer::create();

$endpoint = new \CXml\Endpoint(
    $serializer,
    $dtdValidator,
    $cXmlProcessor
);

//$xmlString could be the body of an incoming http request
$xmlString = '<cXML>...</cXML>';
$result = $endpoint->parseAndProcessStringAsCXml($xmlString);

//$result could be null (i.e. for a Response or Message) or another CXml object which would be the Response to a Request
//you would have to handle the transport yourself
```

### Handling Date vs DateTime

The cXML specification is not perfectly clear about the format of dates and times. The specification says that dates 
should be formatted "in the restricted subset of ISO 8601". That means that the format could either be a full ISO 8601
format with time and timezone information (i.e. 2015-04-14T13:36:00-08:00) or a format without time and timezone
(2015-04-14).

With some fields the actual time of day is not relevant and could lead to confusion. For example, the 
`requestedDeliveryDate` field in `ItemOut`. Real-world experience shows that here it is common to only specify the date.
Althout one could argue that the time of day is still relevant here for real tight on-point deliveries.

To solve this problem we introduced a determined `CXml\Model\Date` class in case of using an explicit 
date (without time). This class extends `DateTime` and is therefore compatible with the rest of the model. The class
enforces a date-only representation (Y-m-d).

#### Serialization

You should use the `CXml\Model\Date` class when generating your object-graph in cases you want to output a date-only
value.

#### Deserialization

When parsing a date-property from a cXML document, the `CXml\Model\Date` will be instantiated **if** a date-only
value was discovered (Y-m-d).

## Extending cXML

### Add custom elements

The definition of cXML is open for extension. There are ways to extend the DTD with overriding existing variables and
therefore adding custom elements. With version 2.1.0 we introduced a way to add custom elements to the cXML model.

To make this happen, we have to build our own DTD file and import the original DTD file in it. We can then add our own
elements and attributes in the variables that are defined in the original DTD file.

TODO this is only really implemented for the Payment node at the moment.

#### Example
An example of a custom DTD file that adds a custom element to the `PaymentReference` element:

```dtd
<!ENTITY % cxml.payment  "( PCard | PaymentToken | PaymentReference* )">

<!ENTITY % elements SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd">
%elements;

<!ELEMENT PaymentReference (Money, IdReference*, Extrinsic*)>
<!ATTLIST PaymentReference
        method CDATA #REQUIRED
        provider CDATA #IMPLIED
        >
```

To use this DTD file for validation as well as for serialization and deserialization, you could save the file next to the
other DTD files from cXML and use `DtdValidator::fromDtdDirectory` just as you would with the original DTD files. Or you
could explicitly load only the new DTD file with `new DtdValidator($arrayOfDtdFilepaths)`.

Also you would probably want newly generated cXML files to point to your DTD file. You can do this by telling the
serializer to use your DTD file: `Serializer::create('http://...publicUrlToYourDtd')`.

Now the new element also has to be known by the serializer. Usually the model classes can be found in `CXml\Model`.
