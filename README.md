# dynamic-dns-cloudflare

In order to provide public access to services located behind a non-fixed IP addess, an A record in CloudFlare needs to be updated periodically with the current IP address.

This package gets the public-facing ip address (at the moment using `ipecho.net`) and makes an api call to CloudFlare to update a record.

## Setup ##
