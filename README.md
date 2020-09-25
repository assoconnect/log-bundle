# Title

[![Build Status](https://travis-ci.org/assoconnect/your-repo.svg?branch=master)](https://travis-ci.org/assoconnect/your-repo)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=assoconnect-your-repo&metric=alert_status)](https://sonarcloud.io/dashboard?id=assoconnect-your-repo)

## Installation

```
composer require assoconnect/your-repo
```

## How-to

* Update the current README replacing `your-repo` with the real name of your repo
* Update the [sonar-project.properties](./sonar-project.properties) file replacing `your-repo` with the real name of your repo
* Update the [composer.json](./composer.json) file replacing `your-repo` with the real name of your repo, and the PSR setting. Add also a description and some keywords
* Create a project at [SonarCloud](https://sonarcloud.io/projects/create) with `assoconnect-your-repo` as key and `your-repo` as display name
* Get a [SonarCloud token](https://sonarcloud.io/account/security/) then add it as the `SONAR_TOKEN` environnement variable on Travis CI at https://travis-ci.com/github/assoconnect/your-repo/settings
* Code must be placed in `src`
* Tests must be placed in `tests`
* Publish it at [Packagist](https://packagist.org/packages/submit)
* Write a relevant README
* Remove this how-to section of the README
