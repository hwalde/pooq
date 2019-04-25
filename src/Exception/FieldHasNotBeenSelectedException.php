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

class FieldHasNotBeenSelectedException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('The Field has not been selected!', 0, $previous);
    }
}