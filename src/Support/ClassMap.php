<?php

namespace TailwindMerge\Support;

use TailwindMerge\ValueObjects\ClassPartObject;
use TailwindMerge\ValueObjects\ClassValidatorObject;
use TailwindMerge\ValueObjects\ThemeGetter;

class ClassMap
{
    final public const CLASS_PART_SEPARATOR = '-';

    /**
     * @param  array{cacheSize: int, prefix: ?string, theme: array<string, mixed>, classGroups: array<string, mixed>,conflictingClassGroups: array<string, array<int, string>>, conflictingClassGroupModifiers: array<string, array<int, string>>}  $config
     */
    public static function create(array $config): ClassPartObject
    {
        $theme = $config['theme'];
        $classGroups = $config['classGroups'];

        $classMap = new ClassPartObject();

        foreach ($classGroups as $classGroupId => $classGroup) {
            self::processClassesRecursively($classGroups[$classGroupId], $classMap, $classGroupId, $theme);
        }

        return $classMap;
    }

    public static function processClassesRecursively(array $classGroup, ClassPartObject $classPartObject, string $classGroupId, array $theme): void
    {
        foreach ($classGroup as $classDefinition) {
            if (is_string($classDefinition)) {
                $classPartObjectToEdit = $classDefinition === '' ? $classPartObject : self::getPart($classPartObject, $classDefinition);
                $classPartObjectToEdit->classGroupId = $classGroupId;

                continue;
            }

            if (self::isThemeGetter($classDefinition)) {
                self::processClassesRecursively(
                    $classDefinition->get($theme),
                    $classPartObject,
                    $classGroupId,
                    $theme,
                );

                continue;
            }

            if (is_callable($classDefinition)) {
                $classPartObject->validators[] = new ClassValidatorObject(
                    classGroupId: $classGroupId,
                    validator: $classDefinition,
                );

                continue;
            }

            foreach ($classDefinition as $key => $classGroup) {
                self::processClassesRecursively(
                    $classGroup,
                    self::getPart($classPartObject, $key),
                    $classGroupId,
                    $theme,
                );
            }
        }
    }

    private static function isThemeGetter(ThemeGetter|array|callable $classDefinition): bool
    {
        return $classDefinition instanceof ThemeGetter;
    }

    private static function getPart(ClassPartObject $classPartObject, string $path): ClassPartObject
    {
        $currentClassPartObject = $classPartObject;

        foreach (explode(self::CLASS_PART_SEPARATOR, $path) as $pathPart) {
            if (! isset($currentClassPartObject->nextPart[$pathPart])) {
                $currentClassPartObject->nextPart[$pathPart] = new ClassPartObject();
            }

            $currentClassPartObject = $currentClassPartObject->nextPart[$pathPart];
        }

        return $currentClassPartObject;
    }
}
