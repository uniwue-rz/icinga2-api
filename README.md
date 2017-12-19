# Icinga2 Api Client for PHP

This is a simple api client for Icinga2 written in PHP. At the moment it has only
read functions. In the future it will be capable of writing to Icinga2
too.

## Installation

To use this client simply add it to your package requirements with composer:

```lang=bash
composer require rzuw/icinga2-api
```

## Configuration for Client

The following settings should be set to make this client work. 

```lang=php
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
);
```

## Configuration for Icinga2 Backend

The complete configuration for API settings for an Icinga2 backend is available
in [Icinga2 Documentation](https://www.icinga.com/docs/icinga2/latest/doc/12-icinga2-api/). There will be to example for API configuration available
one for Authentication with username and password, the other using a X509 certificate.

## Configure API

To configure the Api settings you need to create the master certificates:

```lang=bash
icinga2 pki new-ca
cd /var/lib/icinga2/certs/
icinga2 pki new-cert --cn your-master-instance-fqdn --csr your-master-instance-fqdn.csr --key your-master-instance-fqdn.key
icinga2 pki sign-csr --csr your-master-instance-fqdn.csr --cert your-master-instance-fqdn.crt
```

Copy the `ca.crt` to your client you will need this for your future authentications.

Then add the following configuration for the API, in `/etc/icinga2/features-available/api.conf`

```lang=conf
/**
 * The API listener is used for distributed monitoring setups.
 */
object ApiListener "api" {
 accept_commands = true
 accept_config = true
 ticket_salt = TicketSalt
}
```

Enable the Api settings with commandline or creating a link:

```lang=bash
# Using Commandline
icinga2 feature eanble api
# Or Using link
ln -s /etc/icinga2/features-available/api.conf /etc/icinga2/features-enabled/api.conf
```

### Using Username and Password

In `/etc/icinga2/conf.d/api-users.conf` add your new user with the given settings:

```lang=config
object ApiUser "your-user"{
  password = "your-password"
  permissions = ["*"]
}
```

To test the settings use curl:

```lang=bash
curl -u your-user:your-password --cacert ca.crt 'https://your-icinga2-domain:5665/v1'
```

### Using X509 Certificate

It is possible to use Icinga2 Api with certificates, to do this you need to create the needed
ca certificate, a client certificate and then sign the certificate with ca. The complete documentation can
be found on [Icinga2 website](https://www.icinga.com/docs/icinga2/latest/doc/06-distributed-monitoring/#manual-certificate-creation).


```lang=bash
cd /var/lib/icinga2/certs/
icinga2 pki new-cert --cn your-client-cn --csr your-client-cn.csr --key your-client-cn.key
icinga2 pki sign-csr --csr your-client-cn.csr --cert your-client-cn.crt
```

In `/etc/icinga2/conf.d/api-users.conf` add your new user with the given settings:

```lang=config
object ApiUser "your-client-user"{
  client_cn = "your-client-cn"
  permissions = ["*"]
}
```

Restart the Icinga2 Daemon:

```lang=bash
systemctl restart icinga2
```

Copy `your-client-cn.crt` and `your-client-cn.key` to your Api client and
they can be used.

To test the clients simply use the following `curl` command:

```lang=bash
curl --cert your-client-cn.crt --key your-client-cn.key --cacert ca.crt 'https://your-icinga-api-domain:5665/v1'
# On Successful attempt you will see:
# <html><head><title>Icinga 2</title></head><h1>Hello from Icinga 2 (Version: r2.8.0-1)!</h1><p>You are authenticated as <b>your-client-user</b>. Your user has the following permissions:</p> <ul><li>*</li></ul><p>More information about API requests is available in the <a href="https://docs.icinga.com/icinga2/latest" target="_blank">documentation</a>.</p></html>
```

## Usage

To use this client simply create an instance of Icinga2Api class and
load the configuration inside.

```lang=php
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
);
$icinga2 = new Icinga2($config);
$matchedHosts = $icinga2->getHosts(array("match(\"" . $this->hostData["hostname"] . "*\",host.name)"
```

See the test cases for more examples.

## Testing

To test this client you need to have `phpunit` installed. And the following
environmental variables should be set.

```lang=bash
export ICINGA2HOST="Your API HOST"
export ICINGA2PORT="YOUR API PORT"
export ICINGA2USERNAME= ""
export ICINGA2PASSWORD=""
export ICINGA2CERTPATH=""
export ICINGA2KEYPATH=""
export ICINGA2CAPATH=""
export ICINGA2KEYPASSWORD=""
```