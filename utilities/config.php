<?php
date_default_timezone_set("UTC");

// FOR MORE INFO CHECK THE DOCUMENT - https://doc.rimit.co/getting-started/readme#rest
define('BASE_URL', 'https://uat.rimit.co/api/v1'); // FOR UAT API
//  define('BASE_URL', 'https://api.rimit.co/api/v1'); // FOR PRODUCTION API

// FOR MORE INFO CHECK THE DOCUMENT - https://doc.rimit.co/getting-started/readme#multi-tenant
define('IS_MULTY_TENANT_PLATFORM', 'YES'); // OPTIONS - YES/NO
define('MULTY_TENANT_MODE', 'QUERY'); // OPTIONS - QUERY/PARAMS

?>