<?php

return [
    'http_provider'     => env('ETHEREUM_HTTP_PROVIDER', ''),
    'private_key'       => env('ETHEREUM_PRIVATE_KEY', ''),
    'chain_id'          => (int) env('ETHEREUM_CHAIN_ID', 11155111),

    // contrato (opcional)
    'contract_address'  => env('ETHEREUM_CONTRACT_ADDRESS', ''),
    'contract_abi_path' => env('ETHEREUM_CONTRACT_ABI_PATH', ''),
];
