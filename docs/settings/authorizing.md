# Connecting to Salesforce

_Navigate in the admin panel to: __Salesforce__ > __Settings__ > __Connections___

---

Multiple connections can be configured to communicate among various Salesforce Instances, API versions, and authorization types.

::: warning
Note: Providers are not installed by default.  Choose one of the Provider options below.
:::

## Providers
A connection provider is responsible for authorizing with the Salesforce API.  Salesforce supports various authorization methods; we recommend OAuth.

### Option 1: OAuth 2 _(recommended)_
[Download and install](https://github.com/flipboxfactory/patron-salesforce) our OAuth 2 connection provider.  It leverages [Patron](https://github.com/flipboxfactory/patron), our OAuth2 client manager for Craft CMS.

### Option 2: Build Your Own
You can build your own connection type to accommodate unique connection requirements.

#### Step 1 - Implement Interface
Create a new PHP class that implements [SavableConnectionInterface](https://github.com/flipboxfactory/craft-salesforce/blob/master/src/connections/SavableConnectionInterface.php)

#### Step 2 - Register Provider
Register the provider so it is available as a connection provider option.

Add the following to your Craft Module/Plugin `init()` function.

```php
\yii\base\Event::on(
    \flipbox\craft\salesforce\cp\Cp::class,
    \flipbox\craft\salesforce\events\RegisterConnectionsEvent::REGISTER_CONNECTIONS,
    function (\flipbox\craft\salesforce\events\RegisterConnectionsEvent $event) {
        $event->connections[] = <YOUR_CONNECTION_PROVIDER_CLASS>::class;
    }
);
```

## Overrides
You can define connection provider overrides via: `config/salesforce-connections.php`

An example override may look like:

```php
<?php

return [
    '<YOUR-CONNECTION-HANDLE>' => [
        'version' => 'v45.0'
    ]
];

```


## Pro-Tip
#### How to handle Salesforce Sandbox Instances
We insist on using them during development and testing.  As your Craft project progresses through environments, configure
the connection using [Overrides](#overrides) (depending on the connection provider) to update credentials and endpoints.

#### Which version should I use?
Use the latest most stable version available to your instance.  The version must be entered with a lowercase 'v' prefixed to it.  Ex: `v45.0`.
[Here is a handy reference to determine your API Version](https://help.salesforce.com/articleView?id=000334996&type=1&mode=1). 

