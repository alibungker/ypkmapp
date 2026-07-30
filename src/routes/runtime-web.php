<?php

// Runtime route entrypoint. Seluruh route aplikasi berada pada source yang terversi.
require __DIR__.'/auth.php';
require __DIR__.'/../src/routes/web.php';
