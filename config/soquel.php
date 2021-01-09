<?php

return [
    // Laravel app directory path where object classes lives
    'app_path' => 'Objects',

    /** Salesforce client to fetch query results, default client is AhmadWaleed\Soquel\SOQLClient::class
    * This Package uses omniphx/forrest @see https://github.com/omniphx/forrest package as salesforce client to fetch
    * records from salesforce,  please refer to package github page for installation and configuration guide.
    * If you want to use your own client implementation please make sure you implement AhmadWaleed\Soquel\QueryableInterface
    * and register it in your AppServiceProvider register method.
    * example: client => new CustomClient()
    */
    'client' => null,
];
