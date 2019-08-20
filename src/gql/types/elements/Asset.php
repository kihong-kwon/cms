<?php
namespace craft\gql\types\elements;

use craft\elements\Asset as AssetElement;
use craft\gql\interfaces\elements\Asset as AssetInterface;
use craft\gql\interfaces\Element as ElementInterface;
use craft\gql\base\ObjectType;
use craft\helpers\StringHelper;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class Asset
 */
class Asset extends ObjectType
{
    /**
     * @inheritdoc
     */
    public function __construct(array $config)
    {
        $config['interfaces'] = [
            AssetInterface::getType(),
            ElementInterface::getType(),
        ];

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($source, $arguments, $context, ResolveInfo $resolveInfo)
    {
        /** @var AssetElement $source */
        $fieldName = $resolveInfo->fieldName;

        if (StringHelper::substr($fieldName, 0, 6) === 'volume') {
            $volume = $source->getVolume();
            $property = StringHelper::lowercaseFirst(StringHelper::substr($fieldName, 6));

            return $volume->$property;
        }

        if (StringHelper::substr($fieldName, 0, 6) === 'folder') {
            $folder = $source->getFolder();
            $property = StringHelper::lowercaseFirst(StringHelper::substr($fieldName, 6));

            return $folder->$property;
        }

        return $source->$fieldName;
    }

}
