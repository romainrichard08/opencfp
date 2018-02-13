<?php

declare(strict_types=1);

/**
 * Copyright (c) 2013-2018 OpenCFP
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/opencfp/opencfp
 */

namespace OpenCFP\Infrastructure\Auth;

final class RoleNotFoundException extends \RuntimeException
{
    public static function fromName(string $name): self
    {
        return new self(
            \sprintf(
                'Unable to find a role with name "%s".',
                $name
            )
        );
    }
}
