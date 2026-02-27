<?php
declare(strict_types=1);

namespace FunTask\Domain;

final class Category
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var Tag[] */
    private $tags;

    /** @var Category[] */
    private $children;

    /**
     * @param Tag[] $tags
     * @param Category[] $children
     */
    public function __construct(string $id, string $name, array $tags = [], array $children = [])
    {
        $id = trim($id);
        $name = trim($name);

        if ($id === '') throw new \InvalidArgumentException('Category id must not be empty');
        if ($name === '') throw new \InvalidArgumentException('Category name must not be empty');

        $this->id = $id;
        $this->name = $name;
        $this->tags = $tags;
        $this->children = $children;
    }

    public function id(): string { return $this->id; }
    public function name(): string { return $this->name; }

    /** @return Tag[] */
    public function tags(): array { return $this->tags; }

    /** @return Category[] */
    public function children(): array { return $this->children; }

    public function hasTagType(string $type): bool
    {
        foreach ($this->tags as $tag) {
            if ($tag->isType($type)) return true;
        }
        return false;
    }

    public function hasTag(string $type, ?string $value = null): bool
    {
        foreach ($this->tags as $tag) {
            if ($tag->is($type, $value)) return true;
        }
        return false;
    }

    /** @return string[] */
    public function tagValues(string $type): array
    {
        $values = [];
        foreach ($this->tags as $tag) {
            if ($tag->isType($type) && $tag->value() !== null) {
                $values[] = $tag->value();
            }
        }
        return $values;
    }
}
