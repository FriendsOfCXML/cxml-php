<?php

call_user_func(static function (): void {
    if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
        throw new RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
    }

    require $autoloadFile;
});
