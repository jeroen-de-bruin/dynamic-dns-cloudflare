# dynamic-dns-cloudflare

In order to provide public access to services located behind a non-fixed IP addess, an A record in CloudFlare needs to be updated periodically with the current IP address.

This package gets the public-facing ip address (at the moment using `ipecho.net`) and makes an api call to CloudFlare to update a record.

## Setup ##

### Configuration ###

1. Run `$ composer instal` to install the required dependencies.

2. Copy the `.env` file to `.env.local` or to an environment related file like `.app.dev.local` and set the appropriate values.

    At this moment `APP_EMAIL` is not used and not necessary.

3. Check the commands available using `bin/console`.

## Examples ##

Get the CloudFlare user details:

`$ bin/console jdd:cloudflare:getuserdetails`
