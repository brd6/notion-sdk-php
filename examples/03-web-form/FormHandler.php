<?php

declare(strict_types=1);

use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\Resource\Page;
use Brd6\NotionSdkPhp\Resource\Page\Parent\DatabaseIdParent;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\EmailPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\RichTextPropertyValue;
use Brd6\NotionSdkPhp\Resource\Page\PropertyValue\TitlePropertyValue;
use Brd6\NotionSdkPhp\Resource\RichText\Text;

class FormHandler
{
    private Client $notion;
    private string $databaseId;

    public function __construct(Client $notion, string $databaseId)
    {
        $this->notion = $notion;
        $this->databaseId = $databaseId;
    }

    public function validateFormData(array $data): array
    {
        $errors = [];
        $cleanData = [];

        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        } else {
            $cleanData['name'] = trim($data['name']);
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        } else {
            $cleanData['email'] = trim($data['email']);
        }

        if (empty($data['message'])) {
            $errors[] = 'Message is required';
        } else {
            $cleanData['message'] = trim($data['message']);
        }

        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        return $cleanData;
    }

    public function createNotionPage(array $validatedData): string
    {
        $database = $this->notion->databases()->retrieve($this->databaseId);
        $properties = $database->getProperties();
        
        $titlePropertyName = null;
        $emailPropertyName = null;
        $messagePropertyName = null;

        foreach ($properties as $propertyName => $property) {
            if ($property->getType() === 'title') {
                $titlePropertyName = $propertyName;
            } elseif ($property->getType() === 'email') {
                $emailPropertyName = $propertyName;
            } elseif ($property->getType() === 'rich_text') {
                $messagePropertyName = $propertyName;
            }
        }

        $page = new Page();
        $parent = (new DatabaseIdParent())->setDatabaseId($this->databaseId);
        $page->setParent($parent);

        $pageProperties = [];

        if ($titlePropertyName) {
            $titleProperty = (new TitlePropertyValue())->setTitle([Text::fromContent($validatedData['name'])]);
            $pageProperties[$titlePropertyName] = $titleProperty;
        }

        if ($emailPropertyName) {
            $emailProperty = (new EmailPropertyValue())->setEmail($validatedData['email']);
            $pageProperties[$emailPropertyName] = $emailProperty;
        }

        if ($messagePropertyName) {
            $messageProperty = (new RichTextPropertyValue())->setRichText([Text::fromContent($validatedData['message'])]);
            $pageProperties[$messagePropertyName] = $messageProperty;
        }

        $page->setProperties($pageProperties);

        $createdPage = $this->notion->pages()->create($page);

        return $createdPage->getUrl();
    }
} 