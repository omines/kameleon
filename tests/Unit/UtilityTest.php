<?php

/*
 * KaMeLeon - KML and KMZ reader/writer
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Omines\Kameleon\Util\ErrorToExceptionTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ErrorToExceptionTransformer::class)]
class UtilityTest extends TestCase
{
    public function testSuccessfulErrorTransformation(): void
    {
        $this->expectException(ErrorException::class);
        ErrorToExceptionTransformer::run(static function () {
            trigger_error('This is a test error', E_USER_ERROR);
        });
    }
}
