<?php

declare(strict_types=1);

namespace TheToster\Container;


use Psr\Container\ContainerExceptionInterface;

final class NotResolvable extends \RuntimeException implements ContainerExceptionInterface
{

}
