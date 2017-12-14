# Icinga2 Api Client for PHP

This is a simple api client for Icinga2 written in PHP. At the moment it has only
read functions. In the future it will be capable of writing to Icinga2
too.

## Configuration for Client

The following settings should be set to make this client work. 

```php
$config = array
(
    "host" => ""
    "port" => ""
    // should be set when you decide for username and password login
    "user" => ""
    "password" => ""
    // should be used when you decide for certificate login
    "cert" => ""
    "key" => ""
    "ca" => ""
    // should be used when you have a password protected key
    "keyPassword" => ""
);
```

## Configuration for Icinga2 Backend

The complete configuration for API settings for an Icinga2 backend is available
in Icinga2 Documentation. There will be to example for API configuration available
one for Authentication with username and password, the other using a X509 certificate.

### Using Username and Password

### Using X509 Certificate

## Testing

To test this client you need to have `phpunit` installed. And the following
environmental variables should be set.

```bash
export ICINGA2HOST="Your API HOST"
export ICINGA2PORT="YOUR API PORT"
export ICINGA2USERNAME= ""
export ICINGA2PASSWORD=""
export ICINGA2CERTPATH=""
export ICINGA2KEYPATH=""
export ICINGA2CAPATH=""
export ICINGA2KEYPASSWORD=""
```