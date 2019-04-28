<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ;

class POOQ
{
    /** @var \PDO */
    private static $pdo;

    public static function initializeUsingExistingPdo(\PDO $pdo) {
        self::$pdo = $pdo;
    }

    public static function initilize(string $databaseName, string $databaseUsername, string $databasePassword,
                                     string $databaseHostname, int $databasePort = 3306, ?string $charset = 'utf8') {
        $databaseHostname .= ':'.$databasePort;
        $charset = isset($charset) ? ';charset='.$charset : '';
        self::$pdo = new \PDO('mysql:host='.$databaseHostname.';dbname=' . $databaseName . $charset, $databaseUsername, $databasePassword);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getPdo(): \PDO
    {
        if(!isset(self::$pdo)) {
            throw new \Error('You need to call POOQ::initialize() first!');
        }
        return self::$pdo;
    }
}