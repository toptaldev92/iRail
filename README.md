# iRail

[![License AGPL-3.0](https://img.shields.io/badge/license-AGPL--3.0-brightgreen.svg)](http://www.gnu.org/licenses/agpl-3.0.html) [![Join the chat at https://gitter.im/iRail/iRail](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/iRail/iRail?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

iRail supports digital creativity concerning mobility in Belgium. This is an attempt to make the railway time schedules in Belgium easily available for anyone. 

Our main site consists of a very easy mobile website to look up time schedules using our own API.

Native applications using the iRail API and created or supported by the iRail team are named BeTrains and RailerApp.

All information can be found on [our blog at hello.iRail.be](http://hello.irail.be/).

## Installation for development purposes ##

_note: you'll also need to have [nodejs](https://nodejs.org), [composer](http://getcomposer.org) and PHP curl extension installed on your system_

 * Step 1: clone this repo
 * Step 2: `composer install`
 * Step 3: make sure storage is writable: `chmod 777 storage`
 * Step 4: Run your test server: `php -S localhost:8008 -t api`
 * Step 5: Enjoy your own iRail API at http://localhost:8008/connections.php?from=Gent%20Sint%20Pieters&to=Antwerp

## Update stations list ##

Stations are updated through the irail/stations composer package. Just perform a `composer update` in the root of the project

## Integrating train occupancy scores ##

We use user feedback and our own knowledge to tell users how busy a train is thank to the crowdfunding campaign of [Spitsgids](https://spitsgids.be).

Optionally, you can thus also set up the occupancy scores on your local machine, but this is a bit more complicated as you will also need mongodb installed:

 * MongoDB: [installation instructions](https://www.mongodb.com/download-center?jmp=nav#community)
 * MongoDB module for PHP, installable through pecl or your systems package manager

You will then need to import the data (the structural.csv file) in MongoDB that already exists in the [Spitsgids-data repo](https://github.com/osoc16/Spitsgids-data):

```bash
mongoimport -d irail -c structural --type csv --file structural.csv --headerline
```

## More links ##

 * Our mailing list: http://list.irail.be/
 * Our GTFS data dumps: http://gtfs.irail.be/
 * Issue tracker: https://github.com/iRail/iRail/issues
 * Just use our HTTP API: http://api.irail.be/
 * BeTrains for Android app source code: https://github.com/iRail/BeTrains-for-Android
 * Other repositories: https://github.com/iRail
