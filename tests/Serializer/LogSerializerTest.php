<?php

declare(strict_types=1);

namespace AssoConnect\LogBundle\Tests\Serializer;

use AssoConnect\LogBundle\Serializer\LogSerializer;
use AssoConnect\LogBundle\Tests\Functional\Entity\AbstractEntity;
use AssoConnect\LogBundle\Tests\Functional\Entity\Author;
use AssoConnect\LogBundle\Tests\Functional\Entity\Post;
use AssoConnect\LogBundle\Tests\Functional\Entity\Tag;
use AssoConnect\PHPDate\AbsoluteDate;
use AssoConnect\PHPPercent\Percent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as KernelTestCase;

class LogSerializerTest extends KernelTestCase
{
    public function testFormatEntity()
    {
        self::bootKernel();

        $author = new Author();
        $author->setRegisteredAt(new AbsoluteDate('2020-02-18'));

        $tag = new Tag();

        $post = new Post($author);
        $post->addTag($tag);

        $entityManager = self::$container->get(EntityManagerInterface::class);

        $formatter = self::$container->get(LogSerializer::class);
        $this->assertSame(
            array_merge(
                $this->helperFormatEntity($author),
                [
                    'email' => $author->getEmail(),
                    'registeredAt' => '2020-02-18',
                ]
            ),
            $formatter->formatEntity($entityManager, $author)
        );
        $this->assertSame($this->helperFormatEntity($tag), $formatter->formatEntity($entityManager, $tag));
        $this->assertSame(
            array_merge(
                $this->helperFormatEntity($post),
                [
                    'author' => $author->getId(),
                    'tags' => [$tag->getId()],
                ]
            ),
            $formatter->formatEntity($entityManager, $post)
        );
    }
    public function helperFormatEntity(AbstractEntity $entity): array
    {
        return [
            'id' => $entity->getId(),
            'createdAt' => $entity->getCreatedAt()->format(\DateTime::ISO8601),
            'updatedAt' => $entity->getUpdatedAt()->format(\DateTime::ISO8601),
            'deletedAt' => null,
        ];
    }

    /**
     * @dataProvider providerFormatField
     */
    public function testFormatField(AbstractEntity $entity, string $field, $value)
    {
        $formatter = new LogSerializer();
        $this->assertSame($value, $formatter->formatField($entity, $field));
    }
    public function providerFormatField()
    {
        $author = new Author();
        $author->setEmail('email@gmail.com');

        $entity = new Post($author);

        $provider = [];
        $provider[] = [$entity, 'id', $entity->getId()];
        $provider[] = [$entity, 'author.email', $author->getEmail()];

        return $provider;
    }

    /**
     * @dataProvider providerFormatValue
     * @group unit
     */
    public function testFormatValue($value, $formatted)
    {
        $formatter = new LogSerializer();
        $this->assertSame($formatted, $formatter->formatValue($value));
    }
    public function providerFormatValue()
    {
        $provider = [];

        $provider[] = [null, null];
        $provider[] = [true, true];
        $provider[] = ['foo', 'foo'];
        $provider[] = [1, 1];
        $provider[] = [1.5, 1.5];
        $provider[] = [new \DateTime('@1529500134'), '2018-06-20T13:08:54+0000'];
        $provider[] = [new Percent(10), '10'];
        $provider[] = [Money::EUR(100), '100 EUR'];

        $entity = new Author();
        $provider[] = [$entity, $entity->getId()];

        // Array
        foreach ($provider as $set) {
            $provider[] = [[$set[0]], [$set[1]]];
        }

        // Doctrine collection
        $collection = new ArrayCollection();
        $collection->add($entity);
        $provider[] = [$collection, [$entity->getId()]];

        return $provider;
    }
}
