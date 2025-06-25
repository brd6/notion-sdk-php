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
        $data = [];
        $parent = $comment->getParent();

        if ($parent !== null) {
            $data['parent'] = $parent->toArray();
        } elseif (strlen($comment->getDiscussionId()) > 0) {
            $data['discussion_id'] = $comment->getDiscussionId();
        } else {
            throw new InvalidParentException('Either parent.page_id or discussion_id must be provided');
        }

        $richTextData = [];
        foreach ($comment->getRichText() as $richText) {
            $richTextData[] = $richText->toArray();
        }
        $data['rich_text'] = $richTextData;

        if (count($comment->getAttachments()) > 0) {
            $attachmentsData = [];
            foreach ($comment->getAttachments() as $attachment) {
                $attachmentsData[] = $attachment->toArray();
            }
            $data['attachments'] = $attachmentsData;
        }

        $requestParameters = (new RequestParameters())
            ->setPath('comments')
            ->setMethod('POST')
            ->setBody($data);

        $rawData = $this->getClient()->request($requestParameters);

        /** @var Comment $createdComment */
        $createdComment = Comment::fromRawData($rawData);

        return $createdComment;
    }
}
