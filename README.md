# AssoConnectLogBundle

[![Build Status](https://travis-ci.org/assoconnect/log-bundle.svg?branch=master)](https://travis-ci.org/assoconnect/log-bundle)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=assoconnect-log-bundle&metric=alert_status)](https://sonarcloud.io/dashboard?id=assoconnect-log-bundle)


## Installation
```
composer require assoconnect/log-bundle
```

## Description

This Symfony4 bundle provides a system creating a Log entity every time a fully Doctrine-managed entity is persisted,
updated or removed.

The Log entity and the LogFactoryInterface have to be implemented.

A system of include and exclude entities can be used to decide which entities have to be logged.

Log.yaml format:
```
log:
    log_filters:
        includedEntities: ['App\Entity\includeEntity1', 'App\Entity\includeEntity2']
        excludeEntities: ['App\Entity\excludeEntity1', 'App\Entity\excludeEntity2']
```

If both lists are empty, every entities will be logged.
If only includedEntities is empty,
everything will be logged unless the processed entity is
an instanceof OR is_subclass_of at least one element of the exclude list.

If only excludeEntities is empty,
only the entities instanceof OR is_subclass_of at least one element of the include list will be logged.

If both lists are not empty,
the entity has to be an instanceof OR is_subclass_of at least one element of the include list
AND NOT an instanceof or is_subclass_of at least one element of the exclude list.


## How-to

* Publish it at [Packagist](https://packagist.org/packages/submit)
* Remove this how-to section of the README
