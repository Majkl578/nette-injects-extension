<?php

namespace Majkl578\NetteAddons\Injects\DI;

use Majkl578\NetteAddons\Injects\DI\Exception\CompileException;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Reflection\AnnotationsParser;
use Nette\Reflection\ClassType;
use Nette\Utils\Strings;

/**
 * @author Michael Moravec
 */
class InjectsExtension extends CompilerExtension
{
	const NAME = 'injects';

	protected $defaults = array(
		'annotationName' => 'inject',
	);

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$config = $this->getConfig($this->defaults);

		foreach ($builder->getDefinitions() as $def) {
			/** @var $def ServiceDefinition */
			$class = $def->class ?: ($def->factory ? $def->factory->entity : NULL);

			if (!$def->shared || !$class || !class_exists($class)) {
				continue;
			}

			$classes = class_parents($class) + array('@self' => $class);
			foreach ($classes as $class) {
				$rc = ClassType::from($class);
				foreach ($rc->getProperties() as $rp) {
					if (!$rp->hasAnnotation($config['annotationName'])) {
						continue;
					}

					$fullPropName = $rp->getDeclaringClass()->getName() . '::$' . $rp->getName();

					if ($rp->isStatic()) {
						trigger_error('Injects are not supported on static properties, found on ' . $fullPropName . '.', E_USER_WARNING);
						continue;
					}

					$var = (string) $rp->getAnnotation('var');

					if (!$var) {
						throw new CompileException('@var annotation on ' . $fullPropName . ' is missing or empty.');
					}

					$m = Strings::match(
						trim($var),
						'~
							(?<name>\\\\?[a-z][a-z0-9_]*(?:\\\\[a-z][a-z0-9_]*)*)   # class name
							(?<multiple>(?:\[\])?)   # array of types
							\z
						~Aix'
					);

					if (!$m) {
						throw new CompileException('@var annotation on ' . $fullPropName . ' contains invalid value.');
					}

					$type = AnnotationsParser::expandClassName($m['name'], $rp->getDeclaringClass());

					$def->addSetup(
						__NAMESPACE__ . '\Helpers::writeProperty(?, ?, ?, '
							. (!empty($m['multiple'])
								? __NAMESPACE__ . '\Helpers::findServicesOfType(?, $this)'
								: '$this->getByType(?)'
							)
							. ')',
						array('@self', $rp->getDeclaringClass()->getName(), $rp->getName(), $type)
					);
				}
			}
		}
	}

	public static function register(Configurator $configurator, $name = self::NAME)
	{
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) use ($name) {
			$compiler->addExtension($name, new InjectsExtension());
		};
	}
}
