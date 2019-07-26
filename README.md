[![CircleCI](https://circleci.com/gh/thinkshout/bene.svg?style=svg)](https://circleci.com/gh/thinkshout/bene)

## Installing Bene
The preferred way to install Bene is via the 
[Bene-project](https://github.com/thinkshout/bene-project) template.

## Running Tests
These instructions assume you have used Composer to install Bene.

### Behat
    $ cd MYPROJECT/web/profiles/bene
    $ /path/to/MYPROJECT/bin/behat

If necessary, copy `behat.local.yml.sample` to `behat.local.yml`, and update 
the values on `behat.local.yml` to match your local environment.
