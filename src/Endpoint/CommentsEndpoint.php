<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Endpoint;

use Brd6\NotionSdkPhp\Exception\ApiResponseException;
use Brd6\NotionSdkPhp\Exception\HttpResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidFileException;
use Brd6\NotionSdkPhp\Exception\InvalidPaginationResponseException;
use Brd6\NotionSdkPhp\Exception\InvalidParentException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceException;
use Brd6\NotionSdkPhp\Exception\InvalidResourceTypeException;
use Brd6\NotionSdkPhp\Exception\InvalidRichTextException;
use Brd6\NotionSdkPhp\Exception\RequestTimeoutException;
use Brd6\NotionSdkPhp\Exception\UnsupportedFileTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedPaginationResponseTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedParentTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedRichTextTypeException;
use Brd6\NotionSdkPhp\Exception\UnsupportedUserTypeException;
use Brd6\NotionSdkPhp\RequestParameters;
use Brd6\NotionSdkPhp\Resource\Comment;
use Brd6\NotionSdkPhp\Resource\Pagination\CommentResults;
use Brd6\NotionSdkPhp\Resource\Pagination\PaginationRequest;
use Brd6\NotionSdkPhp\Resource\RichText\AbstractRichText;
use Http\Client\Exception;

use function array_merge;
use function count;
use function strlen;

class CommentsEndpoint extends AbstractEndpoint
{
    /**
     * @throws ApiResponseException
     * @throws HttpResponseException
     * @throws InvalidPaginationResponseException
     * @throws RequestTimeoutException
     * @throws UnsupportedPaginationResponseTypeException
     * @throws Exception
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function retrieve(string $blockId, ?PaginationRequest $paginationRequest = null): CommentResults
    {
        $query = ['block_id' => $blockId];

        if ($paginationRequest !== null) {
            $query = array_merge($query, $paginationRequest->toArray());
        }

        $requestParameters = (new RequestParameters())
            ->setPath('comments')
            ->setMethod('GET')
            ->setQuery($query);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var CommentResults $commentResults */
        $commentResults = CommentResults::fromRawData($rawData);

        return $commentResults;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function create(Comment $comment): Comment
    {
        $data = $this->buildCreateData($comment);

        $richTextData = [];
        foreach ($comment->getRichText() as $richText) {
            $richTextData[] = $richText->toArray();
        }
        $data['rich_text'] = $richTextData;

        return $this->requestComment(
            (new RequestParameters())
                ->setPath('comments')
                ->setMethod('POST')
                ->setBody($data),
        );
    }

    /**
     * Creates a comment whose content is written as inline markdown.
     * Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function createFromMarkdown(Comment $comment, string $markdown): Comment
    {
        $data = $this->buildCreateData($comment);
        $data['markdown'] = $markdown;

        return $this->requestComment(
            (new RequestParameters())
                ->setPath('comments')
                ->setMethod('POST')
                ->setBody($data),
        );
    }

    /**
     * A connection can only update comments it created. Requires Notion-Version 2026-03-11.
     *
     * @param AbstractRichText[] $richText
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function update(string $commentId, array $richText): Comment
    {
        $richTextData = [];
        foreach ($richText as $richTextItem) {
            $richTextData[] = $richTextItem->toArray();
        }

        return $this->requestComment(
            (new RequestParameters())
                ->setPath("comments/$commentId")
                ->setMethod('PATCH')
                ->setBody(['rich_text' => $richTextData]),
        );
    }

    /**
     * A connection can only update comments it created. Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function updateFromMarkdown(string $commentId, string $markdown): Comment
    {
        return $this->requestComment(
            (new RequestParameters())
                ->setPath("comments/$commentId")
                ->setMethod('PATCH')
                ->setBody(['markdown' => $markdown]),
        );
    }

    /**
     * A connection can only delete comments it created. Requires Notion-Version 2026-03-11.
     *
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    public function delete(string $commentId): Comment
    {
        return $this->requestComment(
            (new RequestParameters())
                ->setPath("comments/$commentId")
                ->setMethod('DELETE'),
        );
    }

    /**
     * @throws InvalidParentException
     */
    private function buildCreateData(Comment $comment): array
    {
        $data = [];
        $parent = $comment->getParent();

        if ($parent !== null) {
            $data['parent'] = $parent->toArray();
        } elseif (strlen($comment->getDiscussionId()) > 0) {
            $data['discussion_id'] = $comment->getDiscussionId();
        } else {
            throw new InvalidParentException('Either parent.page_id or discussion_id must be provided');
        }

        if (count($comment->getAttachments()) > 0) {
            $attachmentsData = [];
            foreach ($comment->getAttachments() as $attachment) {
                $attachmentsData[] = $attachment->toArray();
            }
            $data['attachments'] = $attachmentsData;
        }

        return $data;
    }

    /**
     * @throws ApiResponseException
     * @throws Exception
     * @throws HttpResponseException
     * @throws InvalidFileException
     * @throws InvalidParentException
     * @throws InvalidResourceException
     * @throws InvalidResourceTypeException
     * @throws InvalidRichTextException
     * @throws RequestTimeoutException
     * @throws UnsupportedFileTypeException
     * @throws UnsupportedParentTypeException
     * @throws UnsupportedRichTextTypeException
     * @throws UnsupportedUserTypeException
     */
    private function requestComment(RequestParameters $requestParameters): Comment
    {
        $rawData = $this->getClient()->request($requestParameters);

        /** @var Comment $comment */
        $comment = Comment::fromRawData($rawData);

        return $comment;
    }
}
