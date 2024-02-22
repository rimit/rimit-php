<?php

date_default_timezone_set("Asia/Kolkata");

// FOR MORE INFO CHECK THE DOCUMENT - https://developer.rimit.in/getting-started/readme
define('BASE_URL', 'https://uat-gateway.rimit.in/api/client/rimit/v1'); // FOR UAT API
//define('BASE_URL', 'https://api-gateway.rimit.in/api/client/rimit/v1'); // FOR PRODUCTION API

// FOR MORE INFO CHECK THE DOCUMENT - https://developer.rimit.in/getting-started/readme#multi-tenant
define('IS_MULTY_TENANT_PLATFORM', 'NO'); // OPTIONS - YES/NO
define('MULTY_TENANT_MODE', 'QUERY'); // OPTIONS - QUERY/PARAMS

?>
