<?php
namespace craft\gql\types\generators;

use Craft;
use craft\base\Field;
use craft\elements\GlobalSet as GlobalSetElement;
use craft\gql\base\GeneratorInterface;
use craft\gql\interfaces\elements\GlobalSet as GlobalSetInterface;
use craft\gql\GqlEntityRegistry;
use craft\gql\types\elements\GlobalSet;
use craft\helpers\Gql as GqlHelper;

/**
 * Class GlobalSetType
 */
class GlobalSetType implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public static function generateTypes($context = null): array
    {
        $globalSets = Craft::$app->getGlobals()->getAllSets();
        $gqlTypes = [];

        foreach ($globalSets as $globalSet) {
            /** @var GlobalSetElement $globalSet */
            $typeName = self::getName($globalSet);
            $requiredContexts = GlobalSetElement::getGqlScopesByContext($globalSet);

            if (!GqlHelper::isTokenAwareOf($requiredContexts)) {
                continue;
            }

            $contentFields = $globalSet->getFields();
            $contentFieldGqlTypes = [];

            /** @var Field $contentField */
            foreach ($contentFields as $contentField) {
                $contentFieldGqlTypes[$contentField->handle] = $contentField->getContentGqlType();
            }

            $globalSetFields = array_merge(GlobalSetInterface::getFields(), $contentFieldGqlTypes);

            // Generate a type for each entry type
            $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new GlobalSet([
                'name' => $typeName,
                'fields' => function () use ($globalSetFields) {
                    return $globalSetFields;
                }
            ]));

        }

        return $gqlTypes;
    }

    /**
     * @inheritdoc
     */
    public static function getName($context = null): string
    {
        /** @var GlobalSetElement $context */
        return $context->handle . '_GlobalSet';
    }
}
