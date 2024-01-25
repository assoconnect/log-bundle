# AssoConnectLogBundle

[![Build Status](https://github.com/assoconnect/log-bundle/actions/workflows/build.yml/badge.svg)](https://github.com/assoconnect/log-bundle/actions/workflows/build.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=assoconnect_log-bundle&metric=alert_status)](https://sonarcloud.io/dashboard?id=assoconnect_log-bundle)


## Installation
```
composer require assoconnect/log-bundle
```

## Description

This Symfony5 bundle provides a system creating a Log entity every time a fully Doctrine-managed entity is persisted, updated or removed.

The Log entity and the LogFactoryInterface have to be implemented.

A system of included and excluded entities can be used to decide which entities have to be logged.

Log.yaml format:
```
log:
    log_filters:
        includedEntities: ['App\Entity\includedEntity1', 'App\Entity\includedEntity2']
        excludeEntities: ['App\Entity\excludedEntity1', 'App\Entity\excludedEntity2']
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
