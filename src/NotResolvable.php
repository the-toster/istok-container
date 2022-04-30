<?php

declare(strict_types=1);

namespace Istok\Container;


use Psr\Container\ContainerExceptionInterface;

final class NotResolvable extends \RuntimeException implements ContainerExceptionInterface
{

}
