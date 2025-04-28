# Mage2 Module Vendor CustomOrderProcessing

    ``Vendor/module-CustomOrderProcessing``

### Type 1: Zip file

 - Unzip the zip file in `app/code/Vendor`
 - Enable the module by running `php bin/magento module:enable Vendor_CustomOrderProcessing`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Compile code by running `php bin/magento setup:di:compile`\*
 - Deploy code by running `sudo php bin/magento setup:static-content:deploy -f`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Specifications

 - API Endpoint - POST- /rest/V1/setCustomOrderProcessing
	
Request-

{
"orderId":"3000005",
"status":"shippedf"
}

Response-

[
  {
    "data": "23",
    "status": 1
  }
]


