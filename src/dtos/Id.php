<?php
class Id
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}

function parseId(array $data): array
{
    if (!isset($data['id']) || trim($data['id']) === '') {
        return ['isSuccess' => false, 'message' => 'id is required'];
    }
    if (!ctype_digit(trim($data['id']))) {
        return ['isSuccess' => false, 'message' => 'invalid id format'];
    } else {
        $id = (int) $data['id'];
        return array('isSuccess' => true, 'object' => new Id($id));
    }
}
