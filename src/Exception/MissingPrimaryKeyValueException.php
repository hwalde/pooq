<?php declare(strict_types=1);
/**
 * This file is part of POOQ.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
namespace POOQ\Exception;

class MissingPrimaryKeyValueException extends \Exception
{
    public function __construct(
        $messagePrefix,
        string $recordClassName,
        string $fieldName
    ) {
        $message = $messagePrefix.' '.$recordClassName.' is missing value for field "'.$fieldName.'". ';
        $message .= 'This error message appears as well if the foreignKey columns weren\'t loaded from the database';
        $message .= 'Setting the foreignKey columns directly using the '.$recordClassName.'->set'.
            strtoupper($fieldName[0]).substr($fieldName, 1).' is not permitted! ';
        $message .= 'In case you want to forcefully set them use Reflection to access the RecordValue object ';
        $message .= 'directly in the same way as the RecordMapper does.';
        parent::__construct($message);
    }
}