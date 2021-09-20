# phpack
Yet another way to build php cli executables

IMPORTANT: Windows is currently not supported!

### install
lets install the sucker

```

$ curl https://get.phpack.dev/latest -o phpack

$ chmod +x phpack

And finally move the makephp file into your preferred bin directory.
Or let makephp move itself to 'usr/local/bin' by running:

$ phpack self:install

```

## Initialize

```

$ phpack init

> phpack => initialize project

? Output filename? (myapp) : myapp
? Application entry? (main.php) : main.php

> Setting up basic project structure...
✓ Created ./build/release/.keep
✓ Created ./build/bootstrap.php
✓ Created ./build/debug/.keep
✓ Created ./autoloader.php
✓ Created ./build/log.json
✓ Created ./myapp
✓ Created ./app/.keep
✓ Created ./pack.php
✓ Created ./main.php

> Successfully initialized phpack!

```

## Build

```

$ phpack build

> phpack => build executable

- Adding Files to executable...

✓ Compressed: main.php
✓ Compressed: app/.keep

> Added 2 files to myapp.

✓ Added bootstrap stub to myapp
✓ Changed file permissions

>  Successfully created myapp executable

```

## Usage

```
$ phpack help

Build: 6147ccdb2f484 - Time: 1632095451 - Number: 122 - Target: release

Welcome to the phpack help menu.
This are the currently supported commands:

            [ Initialize ]  $ phpack init
                            > Initialize phpack in a project.
                            - Flags:
                              --no-loader: Don't include the default autoloader

                 [ Build ]  $ phpack build {target?}
                            > Build a phar archive of the current project

               [ Install ]  $ phpack self:install
                            > Moves the executable to the /usr/bin directory
                            - Flags:
                              --bin: The bin location you want the executable to be install in. (default: /usr/local/bin)
                              -y: Force all questions to be answered with "yes"

                [ Update ]  $ phpack self:update
                            > Updates the current executable to the latest version

             [ Help Menu ]  $ phpack help
                            > An overview of all available commands and how to access them.
                            - Flags:
                              -b: Include build info in help menu


```