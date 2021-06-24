#Generate Models
1. get current dtd files from http://cxml.org/downloads.html
2. extract files
3. convert cXML.dtd to xsd's via phpStorm Tools/XML Actions/Convert Schema, use "W3C Standard" and save to directory "model/xsd"
4. Remove ds.xsd, xades.xsd, xml.xsd from imports in cXML.xsd, and remove any unresolved element
5. `composer install`
6. `vendor/bin/xsd2php convert model/config.yml model/xsd/cXML.xsd`
