<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Exception;

use Throwable;

class SelectFieldNotFoundException extends \Exception
{
    public function __construct(string $fieldName, Throwable $previous = null)
    {
        $message = 'Did not found select field "'.$fieldName.'"!';
        parent::__construct($message, 0, $previous);
    }
}