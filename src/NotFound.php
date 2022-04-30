<?php

declare(strict_types=1);

namespace Istok\Container;


use Psr\Container\NotFoundExceptionInterface;

final class NotFound extends \InvalidArgumentException implements NotFoundExceptionInterface
{

}
