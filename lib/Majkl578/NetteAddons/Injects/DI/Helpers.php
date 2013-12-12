<?php

namespace Majkl578\NetteAddons\Injects\DI;

use Majkl578\NetteAddons\Injects\Exception\StaticClassException;
use Nette\DI\Container;
use Nette\Object;
use Nette\Reflection\Property;

/**
 * @internal
 * @author Michael Moravec
 */
class Helpers extends Object
{
	public function __construct()
	{
		throw new StaticClassException();
	}

	/**
	 * @param string $type
	 * @param Container $container
	 * @return object[]
	 */
	public static function findServicesOfType($type, Container $container)
	{
		$services = array();

		foreach ($container->findByType($type) as $name) {
			$services[] = $container->getService($name);
		}

		return $services;
	}

	/**
	 * @param object $object
	 * @param string $class
	 * @param string $name
	 * @param object|object[] $value
	 */
	public static function writeProperty($object, $class, $name, $value)
	{
		$rp = new Property($class, $name);

		if (!$rp->isPublic()) {
			$rp->setAccessible(TRUE);
		}

		$rp->setValue($object, $value);
	}
}
