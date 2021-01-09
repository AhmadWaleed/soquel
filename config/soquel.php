<?php

return [
    // Here you can specify the app directory path where all object classes lives.
    'app_path' => 'Objects',

    /**
    * Here you can specify Salesforce client to fetch query results, the default client is AhmadWaleed\Soquel\SOQLClient::class,
    * this package uses omniphx/forrest @see https://github.com/omniphx/forrest package as salesforce client to fetch
    * records from salesforce, please refer to package github page for installation and configuration guide.
    * If you want to use your own client implementation please make sure you implement AhmadWaleed\Soquel\QueryableInterface.
    * example: client => new CustomClient()
    */
    'client' => new \AhmadWaleed\Soquel\SOQLClient,
];
