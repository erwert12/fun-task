<?php
declare(strict_types=1);

namespace FunTask\Domain;

final class CategoryTreeLoader
{
    public function load(string $path): Category
    {
        if (!is_file($path)) {
            throw new \RuntimeException('JSON file not found: ' . $path);
        }

        $json = file_get_contents($path);
        if ($json === false) {
            throw new \RuntimeException('Failed to read file: ' . $path);
        }

        $data = json_decode($json, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON: ' . json_last_error_msg());
        }

        if (!is_array($data)) {
            throw new \RuntimeException('JSON root must be array/object');
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $this->buildCategory($data[0]);
        }

        return $this->buildCategory($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function buildCategory(array $data): Category
    {
        $id = isset($data['id']) && is_scalar($data['id']) ? (string)$data['id'] : '';
        $name = isset($data['name']) && is_scalar($data['name']) ? (string)$data['name'] : '';

        $rawTags = $data['tags'] ?? [];
        if (!is_array($rawTags)) {
            throw new \RuntimeException('tags must be array for id=' . $id);
        }

        $tags = [];
        foreach ($rawTags as $raw) {
            if (!is_string($raw)) {
                throw new \RuntimeException('tag must be string for id=' . $id);
            }
            $tags[] = Tag::fromString($raw);
        }

        $rawChildren = $data['children'] ?? [];
        if (!is_array($rawChildren)) {
            throw new \RuntimeException('children must be array for id=' . $id);
        }

        $children = [];
        foreach ($rawChildren as $childData) {
            if (!is_array($childData)) {
                throw new \RuntimeException('child must be object for id=' . $id);
            }
            $children[] = $this->buildCategory($childData);
        }

        return new Category($id, $name, $tags, $children);
    }
}
