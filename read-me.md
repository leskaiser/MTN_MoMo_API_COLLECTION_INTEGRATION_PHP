# MTN MoMo API Collection Integration PHP

## Introduction

This repository provides a PHP class, `MomoAPI`, to facilitate integration with the MTN Mobile Money (MoMo) API for collection services. This class allows you to create API users, generate API keys, obtain access tokens, and make payment requests to the MTN MoMo platform.

## Features

- **Create API User:** Automatically create an API user for MTN MoMo integration.
- **Generate API Key:** Securely generate an API key for the created API user.
- **Obtain Access Token:** Generate an access token for authorization.
- **Request to Pay:** Initiate payment requests to the MoMo API.
- **Check Transaction Status:** Retrieve the status of a payment request.

## Requirements

- `PHP 7.0` or higher
- `cURL` extension for PHP

## Installation

Clone the repository to your local machine:

```bash
git clone https://github.com/leskaiser/MTN_MoMo_API_COLLECTION_INTEGRATION_PHP.git
```

### Include the `MomoAPI` class in your project:

```php
require_once 'path_to_repository/MomoAPI.php';
```


## Usage

### Initialization

Instantiate the MomoAPI class:
```php
$momo = new MomoAPI();
```


### Configuration
Set the required parameters:


```php
$momo->setMomoPayHost('sandbox.momodeveloper.mtn.com'); // Set MoMo API host
$momo->setPrimaryKey('your_primary_key'); // Set your primary key
$momo->setProviderCallbackHost('your_callback_host'); // Set your provider callback host
$momo->setTargetEnvironment('sandbox'); // Set the target environment (sandbox or live)
```

### Request to Pay
Initiate a payment request:

```php
$phone = '237679465319'; // Payer's phone number
$amount = 100; // Amount to be paid
$currency = 'EUR'; // Currency

$responseCode = $momo->requestToPay($phone, $amount, $currency);

if ($responseCode == 202) {
echo "Payment request initiated successfully.";
} else {
echo "Failed to initiate payment request.";
}
```

### Check Transaction Status
Retrieve the status of a payment request:


```php
$status = $momo->requestToPayTransactionStatus();
echo "Transaction Status: " . json_encode($status);
```



## Methods

### `setMomoPayHost($momo_pay_host)`

Set the MoMo API host.

### `setProviderCallbackHost($providerCallbackHost)`

Set the provider callback host.

### `setPrimaryKey($primary_key)`

Set the primary key.

### `setTargetEnvironment($targetEnvironment)`

Set the target environment (sandbox or live).

### `requestToPay($phone, $amount, $currency)`

Initiate a payment request.

### `requestToPayTransactionStatus()`

Retrieve the status of a payment request.



## Example
Here is an example of using the MomoAPI class in a PHP script:

```php
<?php
require_once 'path_to_repository/MomoAPI.php';

$momo = new MomoAPI();
$momo->setMomoPayHost('sandbox.momodeveloper.mtn.com');
$momo->setPrimaryKey('your_primary_key');
$momo->setProviderCallbackHost('your_callback_host');
$momo->setTargetEnvironment('sandbox');

$phone = '237679465319';
$amount = 100;
$currency = 'EUR';

$responseCode = $momo->requestToPay($phone, $amount, $currency);

if ($responseCode == 202) {
    echo "Payment request initiated successfully.\n";
    $status = $momo->requestToPayTransactionStatus();
    echo "Transaction Status: " . json_encode($status);
} else {
    echo "Failed to initiate payment request.\n";
}

```
## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any changes.

## Contact

For any inquiries, please contact [wanzoou@gmail.com](mailto:wanzoou@gmail.com).