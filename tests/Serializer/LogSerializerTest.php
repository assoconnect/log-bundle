<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Serializer;

use AssoConnect\LogBundle\Entity\Log;
use AssoConnect\LogBundle\Exception\UnsupportObjectException;
use AssoConnect\LogBundle\Serializer\LogSerializer;
use AssoConnect\LogBundle\Tests\Functional\Entity\AbstractEntity;
use AssoConnect\LogBundle\Tests\Functional\Entity\Author;
use AssoConnect\LogBundle\Tests\Functional\Entity\ObjectWithoutId;
use AssoConnect\LogBundle\Tests\Functional\Entity\Post;
use AssoConnect\LogBundle\Tests\Functional\Entity\Tag;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as KernelTestCase;

class LogSerializerTest extends KernelTestCase
{
    public function testFormatEntity(): void
    {
        self::bootKernel();

        $author = new Author();
        $author->setEmail('test@gmail.com');

        $tag = new Tag();

        $post = new Post($author);
        $post->addTag($tag);

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $formatter = self::getContainer()->get(LogSerializer::class);
        self::assertSame(
            json_encode(array_merge(
                $this->helperFormatEntity($author),
                [
                    'email'        => $author->getEmail(),
                    'registeredAt' => $author->getRegisteredAt()->format(\DateTimeInterface::ISO8601),
                    'address'      => $author->getAddress(),
                ]
            ), JSON_PRETTY_PRINT),
            $formatter->formatEntity($entityManager, $author)
        );

        self::assertSame(
            json_encode($this->helperFormatEntity($tag), JSON_PRETTY_PRINT),
            $formatter->formatEntity($entityManager, $tag)
        );
        self::assertSame(
            json_encode(array_merge(
                $this->helperFormatEntity($post),
                [
                    'author' => $author->getId(),
                    'tags'   => [$tag->getId()],
                ]
            ), JSON_PRETTY_PRINT),
            $formatter->formatEntity($entityManager, $post)
        );
    }

    public function helperFormatEntity(AbstractEntity $entity): array
    {
        return [
            'id'        => $entity->getId(),
            'createdAt' => $entity->getCreatedAt()->format(\DateTimeInterface::ISO8601),
            'updatedAt' => $entity->getUpdatedAt()->format(\DateTimeInterface::ISO8601),
        ];
    }

    /**
     * @dataProvider providerFormatValueAsString
     */
    public function testFormatValueAsStringWorks($value, $formatted): void
    {
        $formatter = new LogSerializer();
        self::assertSame($formatted, $formatter->formatValueAsString($value));
    }

    public function providerFormatValueAsString(): iterable
    {
        yield [null, 'null'];
        yield [[null], '[null]'];

        yield [true, 'true'];
        yield [[true], '[true]'];

        yield ['foo', '"foo"'];
        yield [['foo'], '["foo"]'];
        yield [
            str_repeat('a', 70000),
            '"' . str_repeat('a', Log::MAX_STRING_LENGTH) . '"',
        ];

        yield [1, '1'];
        yield [[1], '[1]'];

        yield [1.5, '1.5'];
        yield [[1.5], '[1.5]'];

        yield [new DateTime('@1529500134'), '"2018-06-20T13:08:54+0000"'];
        yield [[new DateTime('@1529500134')], '["2018-06-20T13:08:54+0000"]'];

        yield [new DateTimeZone('Europe/Paris'), '"Europe\/Paris"'];
        yield [[new DateTimeZone('Europe/Paris')], '["Europe\/Paris"]'];

        yield [Money::EUR('100'), '"100 EUR"'];
        yield [[Money::EUR('100')], '["100 EUR"]'];

        yield [new Currency('EUR'), '"EUR"'];
        yield [[new Currency('EUR')], '["EUR"]'];

        $entity = new Author();
        yield [$entity, (string) $entity->getId()];
        yield [[$entity], '[' . $entity->getId() . ']'];

        // Doctrine collection
        $collection = new ArrayCollection();
        $collection->add($entity);
        yield [$collection, '[' . $entity->getId() . ']'];
    }

    public function testUnsupportedObjectThrowsAnException(): void
    {
        $formatter = new LogSerializer();

        $this->expectException(UnsupportObjectException::class);

        $formatter->formatValueAsString(new ObjectWithoutId());
    }
}
