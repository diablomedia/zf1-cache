<?php

declare(strict_types = 1);

if (version_compare(PHP_VERSION, '8.0', '>=')) {
    return array(
        'parameters' => array(
            'ignoreErrors' => array(
                // The signature for ob_implicit_flush changed from int to bool for this parameter in PHP 8
                array(
                    'message' => '#Parameter \#1 \$enable of function ob_implicit_flush expects bool, int given\.#',
                    'path'    => __DIR__ . '/src/Zend/Cache/Frontend/Capture.php',
                    'count'   => 1,
                ),
                array(
                    'message' => '#Parameter \#1 \$enable of function ob_implicit_flush expects bool, int given\.#',
                    'path'    => __DIR__ . '/src/Zend/Cache/Frontend/Class.php',
                    'count'   => 1,
                ),
                array(
                    'message' => '#Parameter \#1 \$enable of function ob_implicit_flush expects bool, int given\.#',
                    'path'    => __DIR__ . '/src/Zend/Cache/Frontend/Function.php',
                    'count'   => 1,
                ),
                array(
                    'message' => '#Parameter \#1 \$enable of function ob_implicit_flush expects bool, int given\.#',
                    'path'    => __DIR__ . '/src/Zend/Cache/Frontend/Output.php',
                    'count'   => 1,
                ),
                array(
                    'message' => '#Parameter \#1 \$enable of function ob_implicit_flush expects bool, int given\.#',
                    'path'    => __DIR__ . '/src/Zend/Cache/Frontend/Page.php',
                    'count'   => 1,
                ),
            )
        )
    );
} else {
    return array();
}
