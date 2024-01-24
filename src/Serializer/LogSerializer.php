<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Serializer;

use AssoConnect\LogBundle\Exception\UnsupportObjectException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class LogSerializer
{
    // Maximum number of associations to log in order to avoid column oversize
    public const ASSOCIATION_MAX_TO_LOG = 1000;

    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function formatEntity(EntityManagerInterface $entityManager, object $entity): string
    {
        $metadata = $entityManager->getClassMetadata(get_class($entity));
        $data = array();

        // Regular fields
        $fields = $metadata->getFieldNames();
        foreach ($fields as $field) {
            $data[$field] = $this->formatField($entity, $field);
        }

        // Associations
        $associations = $metadata->getAssociationNames();
        foreach ($associations as $association) {
            if ($metadata->isAssociationInverseSide($association) === false) {
                $data[$association] = $this->formatField($entity, $association);
                // If the number of associations is greater than the limit then we slice the associations array
                if (is_array($data[$association]) && count($data[$association]) > self::ASSOCIATION_MAX_TO_LOG) {
                    $data[$association] = array_slice($data[$association], 0, self::ASSOCIATION_MAX_TO_LOG);
                }
            }
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function formatValueAsString($value): string
    {
        return json_encode($this->formatValue($value));
    }

    /**
     * Returns a formatted value of a given entities' field
     */
    private function formatField(object $entity, string $field): mixed
    {
        if ($this->propertyAccessor->isReadable($entity, $field)) {
            $value = $this->propertyAccessor->getValue($entity, $field);
            return $this->formatValue($value);
        }

        return null;
    }

    /**
     * Returns a formatted value depending on the given value's type.
     */
    private function formatValue($value): mixed
    {
        switch (gettype($value)) {
            case 'NULL':
            case 'boolean':
            case 'double':
            case 'integer':
            case 'string':
                // Scalar so no need to format it
                return $value;
            case 'object':
                return $this->formatObject($value);
            case 'array':
                // Recursive call on each iterable item
                return array_map(__METHOD__, $value);
            default:
                throw new \DomainException('Unhandled type');
        }
    }

    /**
     * Returns a scalar representation of the object
     */
    private function formatObject(object $value): mixed
    {
        if ($value instanceof Money) {
            // Required because Money does not provide a __toString() method
            // @link https://github.com/moneyphp/money/issues/184
            // Case when we change the amount of a Money in one of our entities.
            // We log amount & currency code for log readability.
            return $value->getAmount() . ' ' . $value->getCurrency()->getCode();
        }

        if (method_exists($value, 'getId')) {
            return $value->getId();
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ISO8601);
        }

        if ($value instanceof \DateTimeZone) {
            return $value->getName();
        }

        if ($value instanceof Collection) {
            return array_map(static function (object $item): mixed {
                return $item->getId();
            }, $value->toArray());
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if (method_exists($value, '__toString')) {
            return $value->__toString();
        }

        throw new UnsupportObjectException($value);
    }
}
